const express = require('express');
const { body } = require('express-validator');
const {
  show,
  update,
  updatePassword,
  destroy,
} = require('../controllers/profileController');
const { protect } = require('../middleware/auth');
const { handleValidation } = require('../utils/validators');

const router = express.Router();

router.use(protect);

router.get('/', show);

router.patch(
  '/',
  [
    body('name').optional().trim().notEmpty().isLength({ max: 255 }),
    body('email').optional().trim().isEmail().normalizeEmail(),
  ],
  handleValidation,
  update
);

router.put(
  '/password',
  [
    body('current_password').notEmpty().withMessage('La contraseña actual es obligatoria'),
    body('password').isLength({ min: 8 }).withMessage('Mínimo 8 caracteres'),
  ],
  handleValidation,
  updatePassword
);

router.delete(
  '/',
  [body('password').notEmpty().withMessage('La contraseña es obligatoria')],
  handleValidation,
  destroy
);

module.exports = router;
