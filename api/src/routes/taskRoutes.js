const express = require('express');
const { body } = require('express-validator');
const {
  index,
  store,
  show,
  update,
  destroy,
} = require('../controllers/taskController');
const { protect } = require('../middleware/auth');
const { handleValidation } = require('../utils/validators');

const router = express.Router();

router.use(protect);

const taskValidators = [
  body('title')
    .if(body('status').not().exists())
    .trim()
    .notEmpty()
    .withMessage('El título es obligatorio')
    .isLength({ max: 255 }),
  body('description')
    .if(body('status').not().exists())
    .trim()
    .notEmpty()
    .withMessage('La descripción es obligatoria'),
  body('date')
    .if(body('status').not().exists())
    .notEmpty()
    .withMessage('La fecha es obligatoria')
    .isISO8601({ strict: false })
    .withMessage('Fecha inválida'),
  body('time')
    .if(body('status').not().exists())
    .notEmpty()
    .withMessage('La hora es obligatoria'),
];

router.get('/', index);
router.post('/', taskValidators, handleValidation, store);
router.get('/:id', show);
router.put('/:id', taskValidators, handleValidation, update);
router.patch('/:id', taskValidators, handleValidation, update);
router.delete('/:id', destroy);

module.exports = router;
