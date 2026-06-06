const mongoose = require('mongoose');

const taskSchema = new mongoose.Schema(
  {
    user: {
      type: mongoose.Schema.Types.ObjectId,
      ref: 'User',
      required: true,
      index: true,
    },
    title: {
      type: String,
      required: [true, 'El título es obligatorio'],
      trim: true,
      maxlength: 255,
    },
    description: {
      type: String,
      required: [true, 'La descripción es obligatoria'],
      trim: true,
    },
    date: {
      type: String,
      required: [true, 'La fecha es obligatoria'],
    },
    time: {
      type: String,
      required: [true, 'La hora es obligatoria'],
    },
    status: {
      type: String,
      enum: ['pending', 'completed', 'vencida'],
      default: 'pending',
    },
  },
  { timestamps: true }
);

module.exports = mongoose.model('Task', taskSchema);
