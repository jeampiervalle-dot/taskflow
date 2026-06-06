const User = require('../models/User');
const Task = require('../models/Task');
const Notification = require('../models/Notification');

exports.show = async (req, res) => {
  res.json({ user: req.user });
};

exports.update = async (req, res, next) => {
  try {
    const { name, email } = req.body;
    const user = req.user;

    if (email && email !== user.email) {
      const exists = await User.findOne({ email });
      if (exists) {
        return res.status(409).json({ message: 'El email ya está en uso' });
      }
      user.email = email;
      user.emailVerifiedAt = null;
    }

    if (name) user.name = name;

    await user.save();

    res.json({ message: 'Perfil actualizado', user });
  } catch (error) {
    next(error);
  }
};

exports.updatePassword = async (req, res, next) => {
  try {
    const { current_password, password } = req.body;
    const user = await User.findById(req.user._id).select('+password');

    const valid = await user.comparePassword(current_password);
    if (!valid) {
      return res.status(401).json({ message: 'Contraseña actual incorrecta' });
    }

    user.password = password;
    await user.save();

    res.json({ message: 'Contraseña actualizada' });
  } catch (error) {
    next(error);
  }
};

exports.destroy = async (req, res, next) => {
  try {
    const { password } = req.body;
    const user = await User.findById(req.user._id).select('+password');

    const valid = await user.comparePassword(password);
    if (!valid) {
      return res.status(401).json({ message: 'Contraseña incorrecta' });
    }

    await Promise.all([
      Task.deleteMany({ user: user._id }),
      Notification.deleteMany({ user: user._id }),
      User.deleteOne({ _id: user._id }),
    ]);

    res.json({ message: 'Cuenta eliminada correctamente' });
  } catch (error) {
    next(error);
  }
};
