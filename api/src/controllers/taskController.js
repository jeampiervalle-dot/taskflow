const Task = require('../models/Task');
const Notification = require('../models/Notification');

const buildDateTime = (date, time) => new Date(`${date}T${time}`);

const findOrCreateNotification = async (filter) => {
  const existing = await Notification.findOne(filter);
  if (existing) return existing;
  return Notification.create(filter);
};

exports.index = async (req, res, next) => {
  try {
    const userId = req.user._id;

    const [tasks, notifications, nextTask, lastEditedTask] = await Promise.all([
      Task.find({ user: userId }).sort({ updatedAt: -1 }),
      Notification.find({ user: userId }).sort({ createdAt: -1 }),
      Task.findOne({ user: userId, status: 'pending' }).sort({ date: 1, time: 1 }),
      Task.findOne({ user: userId }).sort({ updatedAt: -1 }),
    ]);

    let showToast = false;
    let updatedNextTask = nextTask;

    if (nextTask) {
      const fechaLimite = buildDateTime(nextTask.date, nextTask.time);
      const ahora = new Date();
      const diferenciaEnHoras = (fechaLimite - ahora) / (1000 * 60 * 60);

      if (diferenciaEnHoras <= 0) {
        nextTask.status = 'vencida';
        await nextTask.save();
        updatedNextTask = nextTask;

        await findOrCreateNotification({
          user: userId,
          title: 'Tarea vencida',
          message: `🚨 La tarea '${nextTask.title}' ha expirado sin completarse.`,
        });
      } else if (diferenciaEnHoras > 0 && diferenciaEnHoras <= 72) {
        showToast = true;

        const horasTexto =
          diferenciaEnHoras < 24
            ? `${diferenciaEnHoras.toFixed(1)} horas`
            : `${Math.round(diferenciaEnHoras / 24)} días`;

        await findOrCreateNotification({
          user: userId,
          title: 'Tarea pendiente',
          message: `📋 Tarea pendiente: '${nextTask.title}' - Faltan ${horasTexto}.`,
        });
      }
    }

    const refreshedNotifications = await Notification.find({ user: userId }).sort({
      createdAt: -1,
    });

    res.json({
      tasks,
      notifications: refreshedNotifications,
      nextTask: updatedNextTask,
      lastEditedTask,
      showToast,
    });
  } catch (error) {
    next(error);
  }
};

exports.store = async (req, res, next) => {
  try {
    const { title, description, date, time } = req.body;

    const task = await Task.create({
      user: req.user._id,
      title,
      description,
      date,
      time,
    });

    await Notification.create({
      user: req.user._id,
      title: 'Tarea creada',
      message: `✔ Se creó la tarea: '${task.title}'`,
    });

    res.status(201).json({ message: 'Tarea creada', task });
  } catch (error) {
    next(error);
  }
};

exports.show = async (req, res, next) => {
  try {
    const task = await Task.findOne({ _id: req.params.id, user: req.user._id });
    if (!task) {
      return res.status(404).json({ message: 'Tarea no encontrada' });
    }
    res.json(task);
  } catch (error) {
    next(error);
  }
};

exports.update = async (req, res, next) => {
  try {
    const task = await Task.findOne({ _id: req.params.id, user: req.user._id });
    if (!task) {
      return res.status(404).json({ message: 'Tarea no encontrada' });
    }

    if (req.body.status && Object.keys(req.body).length === 1) {
      task.status = req.body.status;
      await task.save();

      await Notification.create({
        user: req.user._id,
        title: 'Tarea terminada',
        message: `🎉 Se completó la tarea: '${task.title}'`,
      });

      return res.json({ message: 'Tarea completada', task });
    }

    const { title, description, date, time } = req.body;
    if (!title || !description || !date || !time) {
      return res.status(422).json({
        message: 'Error de validación',
        errors: {
          title: !title ? ['El título es obligatorio'] : undefined,
          description: !description ? ['La descripción es obligatoria'] : undefined,
          date: !date ? ['La fecha es obligatoria'] : undefined,
          time: !time ? ['La hora es obligatoria'] : undefined,
        },
      });
    }

    const newDateTime = buildDateTime(date, time);
    const now = new Date();

    let status = task.status;
    if (task.status === 'vencida' && newDateTime > now) {
      status = 'pending';
    }

    task.title = title;
    task.description = description;
    task.date = date;
    task.time = time;
    task.status = status;
    await task.save();

    await Notification.create({
      user: req.user._id,
      title: 'Tarea actualizada',
      message: `✏ Se actualizó la tarea: '${task.title}'`,
    });

    res.json({ message: 'Tarea actualizada', task });
  } catch (error) {
    next(error);
  }
};

exports.destroy = async (req, res, next) => {
  try {
    const task = await Task.findOneAndDelete({
      _id: req.params.id,
      user: req.user._id,
    });

    if (!task) {
      return res.status(404).json({ message: 'Tarea no encontrada' });
    }

    await Notification.create({
      user: req.user._id,
      title: 'Tarea eliminada',
      message: `🗑 Se eliminó la tarea: '${task.title}'`,
    });

    res.json({ message: 'Tarea eliminada' });
  } catch (error) {
    next(error);
  }
};
