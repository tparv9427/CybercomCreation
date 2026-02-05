const walletService = require('../services/walletService');
const catchAsync = require('../utils/catchAsync');
const constants = require('../config/constants');

exports.createWallet = catchAsync(async (req, res, next) => {
    // Assuming auth middleware adds user to req.user
    const userId = req.body.userId || (req.user ? req.user.id : null);

    if (!userId) {
        return res.status(400).json({ message: 'User ID required' });
    }

    const wallet = await walletService.createWallet(userId);

    res.status(constants.HTTP_STATUS.CREATED).json({
        status: 'success',
        data: { wallet }
    });
});

exports.getBalance = catchAsync(async (req, res, next) => {
    const userId = req.params.userId;
    const balance = await walletService.getBalance(userId);

    res.status(constants.HTTP_STATUS.OK).json({
        status: 'success',
        data: { balance }
    });
});

exports.addFunds = catchAsync(async (req, res, next) => {
    const { userId, amount } = req.body;
    const wallet = await walletService.addFunds(userId, amount);

    res.status(constants.HTTP_STATUS.OK).json({
        status: 'success',
        data: { wallet }
    });
});
