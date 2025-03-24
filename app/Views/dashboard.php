<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h2>Welcome, <?= session('user.name') ?></h2>
    <p>Email: <?= session('user.email') ?></p>
  

    <h3>Save your phone number to get notified by call </h3>
    <form method="POST" action="<?= site_url('auth/update-phone') ?>">
        <label>Phone number</label>
        <input type="text" pattern="^\+?[0-9]+$" name="phone_number" id="phone_number" value="">
        <input type="submit" value="Update phone number">
    </form>

    <h1>Your Google Calendar Events</h1>

    <?php foreach ($events as $event): ?>
        <li>
            <?= $event->getSummary(); ?> - <?= $event->getStart()->getDateTime(); ?>
        </li>
    <?php endforeach; ?>


    <a href="<?= base_url('auth/logout'); ?>">Logout</a>
</body>
</html>
