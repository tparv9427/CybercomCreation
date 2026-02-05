const winston = require('winston');
const config = require('../config');
const constants = require('../config/constants');

const logger = winston.createLogger({
    level: config.env === 'development' ? constants.LOG_LEVELS.DEBUG : constants.LOG_LEVELS.INFO,
    format: winston.format.combine(
        winston.format.timestamp(),
        winston.format.json()
    ),
    defaultMeta: { service: constants.APP.NAME },
    transports: [
        new winston.transports.File({ filename: 'logs/error.log', level: 'error' }),
        new winston.transports.File({ filename: 'logs/combined.log' }),
    ],
});

if (config.env !== 'production') {
    logger.add(new winston.transports.Console({
        format: winston.format.combine(
            winston.format.colorize(),
            winston.format.simple()
        ),
    }));
}

module.exports = logger;
