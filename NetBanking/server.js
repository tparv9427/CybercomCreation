const app = require('./src/app');
const config = require('./src/config');
const logger = require('./src/utils/logger');
const constants = require('./src/config/constants');

const server = app.listen(config.port, () => {
    logger.info(`${constants.APP.NAME} running on port ${config.port} in ${config.env} mode`);
});

// Handle Unhandled Promise Rejections
process.on('unhandledRejection', (err) => {
    logger.error('UNHANDLED REJECTION! 💥 Shutting down...');
    logger.error(err.name, err.message);
    server.close(() => {
        process.exit(1);
    });
});

// Handle Uncaught Exceptions
process.on('uncaughtException', (err) => {
    logger.error('UNCAUGHT EXCEPTION! 💥 Shutting down...');
    logger.error(err.name, err.message);
    process.exit(1);
});
