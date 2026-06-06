const express = require('express');
const {
  index,
  markAsRead,
  destroy,
} = require('../controllers/notificationController');
const { protect } = require('../middleware/auth');

const router = express.Router();

router.use(protect);

router.get('/', index);
router.patch('/:id/read', markAsRead);
router.delete('/:id', destroy);

module.exports = router;
