const { validationResult } = require('express-validator');

const handleValidation = (req, res, next) => {
  const errors = validationResult(req);
  if (!errors.isEmpty()) {
    const formatted = {};
    errors.array().forEach((e) => {
      if (!formatted[e.path]) formatted[e.path] = [];
      formatted[e.path].push(e.msg);
    });
    return res.status(422).json({
      message: 'Error de validación',
      errors: formatted,
    });
  }
  next();
};

module.exports = { handleValidation };
