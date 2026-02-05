const Wallet = require('../models/Wallet');
const AppError = require('../utils/AppError');
const constants = require('../config/constants');
const logger = require('../utils/logger');
const { sequelize } = require('../services/db');

class WalletService {
    /**
     * Create a new wallet for a user
     * @param {string} userId 
     * @returns {Promise<Wallet>}
     */
    async createWallet(userId) {
        try {
            const wallet = await Wallet.create({ userId });
            return wallet;
        } catch (error) {
            logger.error('Error in createWallet service:', error);
            throw new AppError('Could not create wallet', constants.HTTP_STATUS.INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get wallet balance
     * @param {string} userId
     * @returns {Promise<number>}
     */
    async getBalance(userId) {
        const wallet = await Wallet.findOne({ where: { userId } });
        if (!wallet) {
            throw new AppError('Wallet not found', constants.HTTP_STATUS.NOT_FOUND);
        }
        return wallet.balance;
    }

    /**
     * Add funds to wallet (Mock Transaction)
     * @param {string} userId 
     * @param {number} amount 
     */
    async addFunds(userId, amount) {
        const t = await sequelize.transaction();
        try {
            const wallet = await Wallet.findOne({ where: { userId }, lock: true, transaction: t });
            if (!wallet) throw new AppError('Wallet not found', 404);

            wallet.balance = parseFloat(wallet.balance) + parseFloat(amount);
            await wallet.save({ transaction: t });

            await t.commit();
            return wallet;
        } catch (error) {
            await t.rollback();
            throw error;
        }
    }
}

module.exports = new WalletService();
