const dotenv = require('dotenv');
const path = require('path');

// Load environment variables
// Load environment variables
const envPath = path.join(__dirname, '../../.env');
const result = dotenv.config({ path: envPath });

if (result.error) {
    console.error('DOTENV Error loading file:', envPath, result.error);
} else {
    console.log('DOTENV loaded file:', envPath);
    console.log('DOTENV parsed keys:', Object.keys(result.parsed || {}));
}

console.log('DB_PASSWORD from env:', process.env.DB_PASSWORD);
console.log('DB_PASSWORD type:', typeof process.env.DB_PASSWORD);

const constants = require('./constants');

module.exports = {
    port: process.env.PORT || constants.APP.PORT,
    env: process.env.NODE_ENV || constants.APP.ENV,
    db: {
        host: process.env.DB_HOST || 'localhost',
        user: process.env.DB_USER || 'postgres',
        password: process.env.DB_PASSWORD || '',
        name: process.env.DB_NAME || 'netbanking',
        port: process.env.DB_PORT || 5432,
        dialect: 'postgres'
    },
    jwt: {
        secret: process.env.JWT_SECRET || 'super-secret-key-change-this',
        expiresIn: process.env.JWT_EXPIRES_IN || '15m',
    },
    redis: {
        host: process.env.REDIS_HOST || 'localhost',
        port: process.env.REDIS_PORT || 6379,
    }
};
