<?php

/*
 * session key
 */
define('ADMIN_AUTH_SESSION', 'admin_auth_session');
define('ADMIN_ROLE_SESSION', 'admin_role_session');
define('ADMIN_PERMISSION_SESSION', 'admin_permission_session');
define('ADMIN_MESSAGE_SESSION', 'admin_message_session');
define('MEMBER_AUTH_SESSION', 'member_auth_session');
define('MEMBER_MESSAGE_SESSION', 'member_message_session');

/*
 * 性別
 */
define('GENDER_UNSPECIFIED', 0);
define('GENDER_MALE', 1);
define('GENDER_FEMALE', 2);

/*
 * 啟用狀態
 */
define('STATUS_ACTIVE', 1);
define('STATUS_INACTIVE', 0);

/*
 * 登入日誌 - 操作類型
 */
define('LOGIN_LOG_ACTION_LOGIN', 'login');
define('LOGIN_LOG_ACTION_LOGOUT', 'logout');

/*
 * 登入日誌 - 狀態
 */
define('LOGIN_LOG_STATUS_SUCCESS', 1);
define('LOGIN_LOG_STATUS_FAIL', 0);

/*
 * 會員登入日誌 - 操作類型
 */
define('MEMBER_LOGIN_LOG_ACTION_LOGIN', 'login');
define('MEMBER_LOGIN_LOG_ACTION_LOGOUT', 'logout');
define('MEMBER_LOGIN_LOG_ACTION_REGISTER', 'register');

/*
 * 會員登入日誌 - 狀態
 */
define('MEMBER_LOGIN_LOG_STATUS_SUCCESS', 1);
define('MEMBER_LOGIN_LOG_STATUS_FAIL', 0);

/*
 * 後台訊息頁 - 訊息 key
 */
define('ADMIN_NOTICE_NO_ROLE', 'no_role');

/*
 * 公告類型
 */
define('ANNOUNCEMENT_TYPE_SYSTEM', 1);
define('ANNOUNCEMENT_TYPE_GENERAL', 2);

/*
 * 商品狀態
 */
define('PRODUCT_STATUS_ONLINE', 1);
define('PRODUCT_STATUS_OFFLINE', 0);

/*
 * 商品主打狀態
 */
define('PRODUCT_FEATURED_ON', 1);
define('PRODUCT_FEATURED_OFF', 0);

/*
 * 商品圖片張數上限
 */
define('PRODUCT_MAX_IMAGES', 5);

/*
 * 會員狀態
 */
define('MEMBER_STATUS_ACTIVE', 'active');
define('MEMBER_STATUS_INACTIVE', 'inactive');

