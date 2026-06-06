const express = require('express');
const { body } = require('express-validator');
const { register, login, logout, me } = require('../controllers/authController');
const { protect } = require('../middleware/auth');
const { handleValidation } = require('../utils/validators');

const router = express.Router();

router.post(
  '/register',
  [
    body('name').trim().notEmpty().withMessage('El nombre es obligatorio').isLength({ max: 255 }),
    body('email').trim().isEmail().withMessage('Email inválido').normalizeEmail(),
    body('password')
      .isLength({ min: 8 })
      .withMessage('La contraseña debe tener al menos 8 caracteres'),
  ],
  handleValidation,
  register
);

router.post(
  '/login',
  [
    body('email').trim().isEmail().withMessage('Email inválido').normalizeEmail(),
    body('password').notEmpty().withMessage('La contraseña es obligatoria'),
  ],
  handleValidation,
  login
);

router.post('/logout', protect, logout);
router.get('/me', protect, me);

module.exports = router;
