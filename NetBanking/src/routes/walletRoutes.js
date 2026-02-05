const express = require('express');
const walletController = require('../controllers/walletController');

const router = express.Router();

router.post('/', walletController.createWallet);
router.get('/:userId/balance', walletController.getBalance);
router.post('/add-funds', walletController.addFunds);

module.exports = router;
