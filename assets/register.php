<?php
/**
 * This file is part of Docalist People.
 *
 * Copyright (C) 2017-2019 Daniel Ménard
 *
 * For copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Docalist\People;

// Les scripts suivants ne sont dispos que dans le back-office
add_action('admin_init', function () {
    $base = DOCALIST_PEOPLE_URL;

    wp_register_style(
        'docalist-people-edit-organization',
        "$base/assets/edit-organization.css",
        [],
        '190208'
    );
});
