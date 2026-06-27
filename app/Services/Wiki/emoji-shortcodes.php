<?php

// GitHub-style emoji shortcode → unicode map for the wiki renderer.
// Only names listed here are converted; anything else (e.g. :https:, :00:,
// :digit:, :-------:) is left as literal text, matching GitBook behavior.
// Covers every emoji shortcode found in the wiki plus common extras.

return [
    // Found in the wiki content
    'coin' => '🪙', 'heart' => '❤️', 'book' => '📖', 'books' => '📚',
    'x' => '❌', 'warning' => '⚠️', 'white_check_mark' => '✅', 'tada' => '🎉',
    'shield' => '🛡️', 'gear' => '⚙️', 'bulb' => '💡', 'moneybag' => '💰',
    'clap' => '👏', 'alembic' => '⚗️', 'link' => '🔗',

    // Common UI / status
    'heavy_check_mark' => '✔️', 'ballot_box_with_check' => '☑️',
    'heavy_exclamation_mark' => '❗', 'exclamation' => '❗', 'question' => '❓',
    'grey_exclamation' => '❕', 'grey_question' => '❔', 'bangbang' => '‼️',
    'no_entry' => '⛔', 'no_entry_sign' => '🚫', 'recycle' => '♻️',
    'heavy_plus_sign' => '➕', 'heavy_minus_sign' => '➖',
    'heavy_multiplication_x' => '✖️', 'heavy_division_sign' => '➗',
    'arrow_right' => '➡️', 'arrow_left' => '⬅️', 'arrow_up' => '⬆️', 'arrow_down' => '⬇️',
    'small_red_triangle' => '🔺', 'small_red_triangle_down' => '🔻',
    '100' => '💯', 'checkered_flag' => '🏁', 'triangular_flag_on_post' => '🚩',
    'crossed_flags' => '🎌', 'white_flag' => '🏳️', 'black_flag' => '🏴',

    // Rewards / economy / gaming
    'star' => '⭐', 'star2' => '🌟', 'sparkles' => '✨', 'fire' => '🔥',
    'boom' => '💥', 'zap' => '⚡', 'gem' => '💎', 'crown' => '👑',
    'trophy' => '🏆', 'medal' => '🏅', 'first_place_medal' => '🥇',
    'second_place_medal' => '🥈', 'third_place_medal' => '🥉',
    'dart' => '🎯', 'game_die' => '🎲', 'video_game' => '🎮', 'joystick' => '🕹️',
    'crossed_swords' => '⚔️', 'dagger' => '🗡️', 'bow_and_arrow' => '🏹',
    'hammer' => '🔨', 'hammer_and_wrench' => '🛠️', 'wrench' => '🔧',
    'gift' => '🎁', 'package' => '📦', 'label' => '🏷️', 'bookmark' => '🔖',
    'key' => '🔑', 'lock' => '🔒', 'unlock' => '🔓', 'bell' => '🔔',
    'money_with_wings' => '💸', 'dollar' => '💵', 'credit_card' => '💳',
    'shopping_cart' => '🛒', 'chart_with_upwards_trend' => '📈', 'bar_chart' => '📊',

    // Docs / info
    'scroll' => '📜', 'memo' => '📝', 'page_facing_up' => '📄', 'clipboard' => '📋',
    'pushpin' => '📌', 'round_pushpin' => '📍', 'mag' => '🔍', 'paperclip' => '📎',
    'calendar' => '📅', 'date' => '📆', 'hourglass' => '⏳', 'alarm_clock' => '⏰',
    'information_source' => 'ℹ️', 'speech_balloon' => '💬', 'bookmark_tabs' => '📑',

    // World / places
    'globe_with_meridians' => '🌐', 'earth_americas' => '🌎', 'map' => '🗺️',
    'compass' => '🧭', 'anchor' => '⚓', 'ship' => '🚢', 'house' => '🏠',
    'castle' => '🏯', 'european_castle' => '🏰', 'tent' => '⛺', 'fountain' => '⛲',
    'snowflake' => '❄️', 'sunny' => '☀️', 'crescent_moon' => '🌙', 'droplet' => '💧',

    // Hands / faces
    'thumbsup' => '👍', '+1' => '👍', 'thumbsdown' => '👎', '-1' => '👎',
    'ok_hand' => '👌', 'wave' => '👋', 'raised_hand' => '✋', 'muscle' => '💪',
    'pray' => '🙏', 'point_right' => '👉', 'point_left' => '👈',
    'point_up' => '👆', 'point_down' => '👇', 'eyes' => '👀', 'brain' => '🧠',
    'smile' => '😄', 'grin' => '😁', 'joy' => '😂', 'wink' => '😉',
    'heart_eyes' => '😍', 'sunglasses' => '😎', 'thinking' => '🤔', 'sob' => '😭',
    'skull' => '💀', 'ghost' => '👻', 'alien' => '👽', 'robot' => '🤖',
    'imp' => '👿', 'japanese_ogre' => '👹', 'rocket' => '🚀', 'crystal_ball' => '🔮',
];
