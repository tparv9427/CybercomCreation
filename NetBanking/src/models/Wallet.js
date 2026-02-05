const { DataTypes } = require('sequelize');
const { sequelize } = require('../services/db');
const constants = require('../config/constants');

const Wallet = sequelize.define('Wallet', {
    id: {
        type: DataTypes.UUID,
        defaultValue: DataTypes.UUIDV4,
        primaryKey: true,
    },
    userId: {
        type: DataTypes.UUID,
        allowNull: false,
        // references: { model: 'Users', key: 'id' } // User model not defined yet
    },
    balance: {
        type: DataTypes.DECIMAL(15, 2),
        allowNull: false,
        defaultValue: 0.00,
    },
    currency: {
        type: DataTypes.STRING(3),
        allowNull: false,
        defaultValue: 'INR',
    },
    status: {
        type: DataTypes.STRING,
        defaultValue: 'ACTIVE',
    }
}, {
    tableName: 'wallets',
    timestamps: true,
});

module.exports = Wallet;
