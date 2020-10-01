<?php
/**
 * Создаем страницу настроек плагина
 */

define('OK_OPTION_PAGE_NAME', 'ok_settings');
define('OK_OPTION_GROUP', 'ok_options');

add_action('admin_menu', 'add_plugin_page');
function add_plugin_page(){
    add_options_page( 'Настройка One Click Bay',
        'OneClickBay', 'manage_options',
        'ok_settings',
        'ok_options_page_output' );
}

function ok_options_page_output(){
    ?>
    <div class="wrap">
        <h2><?php echo get_admin_page_title() ?></h2>

        <form action="options.php" method="POST">
            <?php
            settings_fields( OK_OPTION_GROUP );     // скрытые защитные поля
            do_settings_sections( OK_OPTION_PAGE_NAME ); // секции с настройками (опциями). У нас она всего одна 'section_id'
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * Регистрируем настройки.
 * Настройки будут храниться в массиве, а не одна настройка = одна опция.
 */
add_action('admin_init', 'plugin_settings');
function plugin_settings(){
    // параметры: $option_group, $option_name, $sanitize_callback
    register_setting( OK_OPTION_GROUP, 'telegram_bot_token', 'sanitize_callback' );
    register_setting( OK_OPTION_GROUP, 'telegram_chat_id', 'sanitize_callback' );
    register_setting( OK_OPTION_GROUP, 'telegram_trigger', 'sanitize_callback' );
    //Email
    register_setting(OK_OPTION_GROUP, 'ok_email_to', 'sanitize_callback');
    register_setting(OK_OPTION_GROUP, 'ok_email_subject', 'sanitize_callback');
    //reCaptcha
    register_setting(OK_OPTION_GROUP, 'ok_recaptcha_public_key', 'sanitize_callback');
    register_setting(OK_OPTION_GROUP, 'ok_recaptcha_secret_key', 'sanitize_callback');
    register_setting(OK_OPTION_GROUP, 'ok_recaptcha_trigger', 'sanitize_callback');
    //Debug mode
    register_setting(OK_OPTION_GROUP, 'ok_debug_trigger', 'sanitize_callback');

    add_settings_section('ok_email', 'Налаштування Email', function(){
        echo '<p>Налаштування відправки листів</p>';
    }, OK_OPTION_PAGE_NAME);

    add_settings_section('ok_recaptcha', 'Налаштування Google reCaptcha', function(){
        echo '<p>Налаштуйте невидиму капчу (reCaptcha v3) для надійного захисту від спам-ботів</p>';
    }, OK_OPTION_PAGE_NAME);

    // параметры: $id, $title, $callback, $page
    add_settings_section( 'telegram', 'Налаштування telegram', function(){
        echo '<p>Налаштування бота телеграм</p>';
    }, OK_OPTION_PAGE_NAME );

    add_settings_section('ok_debug', 'Для розробників', function(){
        echo '<p>Вмикає логування, вмикайте лише для розробки та налагодження плагіну!!</p>';
    }, OK_OPTION_PAGE_NAME);

    //Email section
    add_settings_field('ok_email_to',
        'Куди відправляти (email)',
        function(){
            $val = get_option('ok_email_to');
            $val = $val ? $val['input']: null;
            echo '<input type="email" 
                name="ok_email_to[input]"
                value="'.esc_attr( $val).'"
            >';
        },
    OK_OPTION_PAGE_NAME,
    'ok_email'
    );
    add_settings_field('ok_email_subject',
        'Тема листа',
        function(){
            $val = get_option('ok_email_subject');
            $val = $val ? $val['input']: null;
            echo '<input type="text" 
                name="ok_email_subject[input]"
                value="'.esc_attr( $val).'"
            >';
        },
        OK_OPTION_PAGE_NAME,
        'ok_email'
    );

    //reCaptcha
    add_settings_field('ok_recaptcha_public_key',
        'Публічний ключ (public key)',
        function(){
            $val = get_option('ok_recaptcha_public_key');
            $val = $val ? $val['input']: null;
            echo '<input type="text" 
                name="ok_recaptcha_public_key[input]"
                value="'.esc_attr( $val).'"
            >';
        },
        OK_OPTION_PAGE_NAME,
        'ok_recaptcha'
    );
    add_settings_field('ok_recaptcha_secret_key',
        'Серкетний ключ (secret key)',
        function(){
            $val = get_option('ok_recaptcha_secret_key');
            $val = $val ? $val['input']: null;
            echo '<input type="text" 
                name="ok_recaptcha_secret_key[input]"
                value="'. esc_attr( $val).'"
            >';
        },
        OK_OPTION_PAGE_NAME,
        'ok_recaptcha'
    );
    add_settings_field('ok_recaptcha_trigger',
        'Увімкнути захист від спаму',
        function(){
            $val = get_option('ok_recaptcha_trigger');
            $val = $val ? $val['checkbox']: null;
            echo '<label><input type="checkbox" 
                name="ok_recaptcha_trigger[checkbox]"
                value="1"
                '. checked(1, $val, false) .'
            > перемикач</label>';
        },
        OK_OPTION_PAGE_NAME,
        'ok_recaptcha'
    );

    //Telegram
    add_settings_field('telegram_bot_token',
        'Токен для доступу до боту телеграм',
        'fill_primer_field1',
        OK_OPTION_PAGE_NAME,
        'telegram' );
    add_settings_field('telegram_chat_id',
        'ID чату тклкграм (якщо використовуєте alias то ставте перед іменем знак @).',
        'fill_chat_id',
        OK_OPTION_PAGE_NAME,
        'telegram' );
    add_settings_field('telegram_trigger',
        'Увімкнути відправлення в Telegram',
        'fill_telegram_trigger',
        OK_OPTION_PAGE_NAME,
        'telegram' );

    //debug mode
    add_settings_field('ok_debug_trigger',
        'Режим розробника',
        function(){
            $val = get_option('ok_debug_trigger');
            $val = $val ? $val['checkbox']: null;
            echo '<label><input type="checkbox" 
                name="ok_debug_trigger[checkbox]"
                value="1"
                '. checked(1, $val, false) .'
            > перимикач</label>';
        },
        OK_OPTION_PAGE_NAME,
    'ok_debug'
    );
}

## Заполняем опцию 1
function fill_primer_field1(){
    $val = get_option('telegram_bot_token');
    $val = $val ? $val['input'] : null;
    ?>
    <input type="text" name="telegram_bot_token[input]" value="<?php echo esc_attr( $val ) ?>" />
    <?php
}

function fill_chat_id(){
    $value = get_option('telegram_chat_id')['input'] ?? null;
    echo ' <input type="text" name="telegram_chat_id[input]" value="'. esc_attr( $value ) .'" />';
}

## Заполняем опцию 2
function fill_telegram_trigger(){
    $val = get_option('telegram_trigger');
    $val = $val ? $val['checkbox'] : null;
    ?>
    <label><input type="checkbox" name="telegram_trigger[checkbox]" value="1" <?php checked( 1, $val ) ?> /> отметить</label>
    <?php
}

## Очистка данных
function sanitize_callback( $options ){
    // очищаем
    foreach( $options as $name => & $val ){
        if( $name == 'input' )
            $val = strip_tags( $val );

        if( $name == 'checkbox' )
            $val = intval( $val );
    }

    //die(print_r( $options )); // Array ( [input] => aaaa [checkbox] => 1 )

    return $options;
}

function ok_sanitize_string($option){
    return strip_tags(trim($option));
}