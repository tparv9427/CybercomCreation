const express = require('express');
const helmet = require('helmet');
const cors = require('cors');
const morgan = require('morgan');
const config = require('./config');
const constants = require('./config/constants');
const logger = require('./utils/logger');
const errorHandler = require('./middleware/errorHandler');
const AppError = require('./utils/AppError');
const { testConnection } = require('./services/db');
const walletRoutes = require('./routes/walletRoutes');

// Initialize Express App
const app = express();

// Security Middleware
app.use(helmet());
app.use(cors());

// Logging Middleware
app.use(morgan('combined', { stream: { write: message => logger.info(message.trim()) } }));

// Body Parser
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Database Connection Test
testConnection();

// Routes
app.use(`${constants.APP.API_PREFIX}/wallet`, walletRoutes);

// Health Check
app.get('/health', (req, res) => {
    res.status(200).json({ status: 'UP', message: 'NetBanking API is running' });
});

// Root Route
app.get('/', (req, res) => {
    res.status(200).json({
        status: 'success',
        message: 'Welcome to NetBanking API',
        version: constants.APP.VERSION
    });
});

// 404 Handler
app.use((req, res, next) => {
    next(new AppError(`${constants.ERROR_MESSAGES.ROUTE_NOT_FOUND} - ${req.originalUrl}`, 404));
});

// Global Error Handler
app.use(errorHandler);

module.exports = app;
