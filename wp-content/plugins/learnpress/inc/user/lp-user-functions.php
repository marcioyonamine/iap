<?php
/**
 * Common functions to process actions about user
 *
 * @author  ThimPress
 * @package LearnPress/Functions/User
 * @version 1.0
 */

/**
 * Delete user data by user ID
 *
 * @param $user_id
 * @param $course_id
 */
function learn_press_delete_user_data( $user_id, $course_id = 0 ) {
	global $wpdb;
	// TODO: Should be deleted user's order and order data???

	$query_args = array( $user_id );
	if ( $course_id ) {
		$query_args[] = $course_id;
	}
	// delete all courses user has enrolled
	$query = $wpdb->prepare( "
				DELETE FROM {$wpdb->prefix}learnpress_user_items
				WHERE user_id = %d
				" . ( $course_id ? " AND item_id = %d" : "" ) . "
			", $query_args );
	@$wpdb->query( $query );
}

function learn_press_get_user_item_id( $user_id, $item_id ) {
	$user_item_ids = LP_Cache::get_user_item_id( false, array() );
	if ( empty( $user_item_ids[$user_id . '-' . $item_id] ) ) {
		global $wpdb;
		$query                                    = $wpdb->prepare( "SELECT user_item_id FROM {$wpdb->prefix}learnpress_user_items WHERE user_id = %d AND item_id = %d ORDER BY user_item_id DESC LIMIT 0,1", $user_id, $item_id );
		$user_item_ids[$user_id . '-' . $item_id] = $wpdb->get_var( $query );
	}
	LP_Cache::set_user_item_id( $user_item_ids );
	return $user_item_ids[$user_id . '-' . $item_id];
}

/**
 * @return int
 */
function learn_press_get_current_user_id() {
	$user = learn_press_get_current_user();
	return $user->id;
}

/**
 * Get the user by $user_id passed. If $user_id is NULL, get current user.
 *
 * If current user is not logged in, return a GUEST user
 *
 * @param int $user_id
 *
 * @return LP_User
 */
function learn_press_get_current_user( $user_id = 0 ) {
	return LP_User_Factory::get_user( $user_id );
}

/**
 * Get user by ID, if the ID is NULL then return a GUEST user
 *
 * @param int  $user_id
 * @param bool $force
 *
 * @return LP_User_Guest|mixed
 */
function learn_press_get_user( $user_id, $force = false ) {
	return LP_User_Factory::get_user( $user_id, $force );
}

/**
 * Add more 2 user roles teacher and student
 *
 * @access public
 * @return void
 */
function learn_press_add_user_roles() {

	$settings = LP()->settings;
	/* translators: user role */
	_x( 'Instructor', 'User role' );
	add_role(
		LP_TEACHER_ROLE,
		'Instructor',
		array()
	);
	$course_cap = LP_COURSE_CPT . 's';
	$lesson_cap = LP_LESSON_CPT . 's';
	$order_cap  = LP_ORDER_CPT . 's';
	// teacher
	$teacher = get_role( LP_TEACHER_ROLE );
	$teacher->add_cap( 'delete_published_' . $course_cap );
	$teacher->add_cap( 'edit_published_' . $course_cap );
	$teacher->add_cap( 'edit_' . $course_cap );
	$teacher->add_cap( 'delete_' . $course_cap );

	$settings->get( 'required_review' );

	if ( $settings->get( 'required_review' ) == 'yes' ) {
		$teacher->remove_cap( 'publish_' . $course_cap );
	} else {
		$teacher->add_cap( 'publish_' . $course_cap );
	}
	//


	$teacher->add_cap( 'delete_published_' . $lesson_cap );
	$teacher->add_cap( 'edit_published_' . $lesson_cap );
	$teacher->add_cap( 'edit_' . $lesson_cap );
	$teacher->add_cap( 'delete_' . $lesson_cap );
	$teacher->add_cap( 'publish_' . $lesson_cap );
	$teacher->add_cap( 'upload_files' );
	$teacher->add_cap( 'read' );
	$teacher->add_cap( 'edit_posts' );

	// administrator
	$admin = get_role( 'administrator' );
	$admin->add_cap( 'delete_' . $course_cap );
	$admin->add_cap( 'delete_published_' . $course_cap );
	$admin->add_cap( 'edit_' . $course_cap );
	$admin->add_cap( 'edit_published_' . $course_cap );
	$admin->add_cap( 'publish_' . $course_cap );
	$admin->add_cap( 'delete_private_' . $course_cap );
	$admin->add_cap( 'edit_private_' . $course_cap );
	$admin->add_cap( 'delete_others_' . $course_cap );
	$admin->add_cap( 'edit_others_' . $course_cap );

	$admin->add_cap( 'delete_' . $lesson_cap );
	$admin->add_cap( 'delete_published_' . $lesson_cap );
	$admin->add_cap( 'edit_' . $lesson_cap );
	$admin->add_cap( 'edit_published_' . $lesson_cap );
	$admin->add_cap( 'publish_' . $lesson_cap );
	$admin->add_cap( 'delete_private_' . $lesson_cap );
	$admin->add_cap( 'edit_private_' . $lesson_cap );
	$admin->add_cap( 'delete_others_' . $lesson_cap );
	$admin->add_cap( 'edit_others_' . $lesson_cap );

	$admin->add_cap( 'delete_' . $order_cap );
	$admin->add_cap( 'delete_published_' . $order_cap );
	$admin->add_cap( 'edit_' . $order_cap );
	$admin->add_cap( 'edit_published_' . $order_cap );
	$admin->add_cap( 'publish_' . $order_cap );
	$admin->add_cap( 'delete_private_' . $order_cap );
	$admin->add_cap( 'edit_private_' . $order_cap );
	$admin->add_cap( 'delete_others_' . $order_cap );
	$admin->add_cap( 'edit_others_' . $order_cap );
}

add_action( 'learn_press_ready', 'learn_press_add_user_roles' );

function learn_press_get_user_questions( $user_id = null, $args = array() ) {
	if ( !$user_id ) {
		$user_id = get_current_user_id();
	}
	return learn_press_get_user( $user_id )->get_questions( $args );
}

/**
 * Get the type of current user
 *
 * @param null $check_type
 *
 * @return bool|string
 */
function learn_press_current_user_is( $check_type = null ) {
	global $current_user;
	$user_roles = $current_user->roles;
	$user_type  = '';

	// backward compatible
	if ( in_array( 'lpr_teacher', $user_roles ) ) {
		$user_type = 'instructor';
	} elseif ( in_array( 'lp_teacher', $user_roles ) ) {
		$user_type = 'instructor';
	} elseif ( in_array( 'administrator', $user_roles ) ) {
		$user_type = 'administrator';
	}
	return $check_type ? $check_type == $user_type : $user_type;
}

function learn_press_user_has_roles( $roles, $user_id = null ) {
	$has_role = false;
	if ( !$user_id ) {
		$user = wp_get_current_user();
	} else {
		$user = get_user_by( 'id', $user_id );
	}
	$available_roles = (array) $user->roles;
	if ( is_array( $roles ) ) {
		foreach ( $roles as $role ) {
			if ( in_array( $role, $available_roles ) ) {
				$has_role = true;
				break; // only need one of roles is in available
			}
		}
	} else {
		if ( in_array( $roles, $available_roles ) ) {
			$has_role = true;
		}
	}
	return $has_role;
}

/**
 * Add user profile link into admin bar
 */
function learn_press_edit_admin_bar() {
	global $wp_admin_bar;
	if ( ( $profile = learn_press_get_page_id( 'profile' ) ) && get_post_type( $profile ) == 'page' && get_post_status( $profile ) != 'trash' && ( LP()->settings->get( 'admin_bar_link' ) == 'yes' ) ) {
		$text                             = LP()->settings->get( 'admin_bar_link_text' );
		$course_profile                   = array();
		$course_profile['id']             = 'course_profile';
		$course_profile['parent']         = 'user-actions';
		$course_profile['title']          = $text ? $text : get_the_title( $profile );
		$course_profile['href']           = learn_press_user_profile_link();
		$course_profile['meta']['target'] = LP()->settings->get( 'admin_bar_link_target' );
		$wp_admin_bar->add_menu( $course_profile );
	}
	$current_user = wp_get_current_user();
	// add `be teacher` link
	if ( in_array( LP_TEACHER_ROLE, $current_user->roles ) || in_array( 'administrator', $current_user->roles ) ) {
		return;
	}
}

add_action( 'admin_bar_menu', 'learn_press_edit_admin_bar' );


function learn_press_current_user_can_view_profile_section( $section, $user ) {
	$current_user = wp_get_current_user();
	$view         = true;
	if ( $user->user_login != $current_user->user_login && $section == LP()->settings->get( 'profile_endpoints.profile-orders', 'profile-orders' ) ) {
		$view = false;
	}
	return apply_filters( 'learn_press_current_user_can_view_profile_section', $view, $section, $user );
}

function learn_press_profile_tab_courses_content( $current, $tab, $user ) {
	learn_press_get_template( 'profile/tabs/courses.php', array( 'user' => $user, 'current' => $current, 'tab' => $tab ) );
}

function learn_press_profile_tab_quizzes_content( $current, $tab, $user ) {
	learn_press_get_template( 'profile/tabs/quizzes.php', array( 'user' => $user, 'current' => $current, 'tab' => $tab ) );
}

function learn_press_profile_tab_orders_content( $current, $tab, $user ) {
	learn_press_get_template( 'profile/tabs/orders.php', array( 'user' => $user, 'current' => $current, 'tab' => $tab ) );
}

//function learn_press_update_user_lesson_start_time() {
//	global $wpdb;
//	$course = LP()->global['course'];
//
//	if ( !$course->id || !( $lesson = $course->current_lesson ) ) {
//		return;
//	}
//        $table = $wpdb->prefix . 'learnpress_user_lessons';
//        if ( $wpdb->get_var("SHOW TABLES LIKE '{$table}'") !== $table ) {
//            return;
//        }
//	$query = $wpdb->prepare( "
//		SELECT user_lesson_id FROM {$wpdb->prefix}learnpress_user_lessons WHERE user_id = %d AND lesson_id = %d AND course_id = %d
//	", get_current_user_id(), $lesson->id, $course->id );
//	if ( $wpdb->get_row( $query ) ) {
//		return;
//	}
//	$wpdb->insert(
//		$wpdb->prefix . 'learnpress_user_lessons',
//		array(
//			'user_id'    => get_current_user_id(),
//			'lesson_id'  => $lesson->id,
//			'start_time' => current_time( 'mysql' ),
//			'status'     => 'stared',
//			'course_id'  => $course->id
//		),
//		array( '%d', '%d', '%s', '%s', '%d' )
//	);
//}
//
//add_action( 'learn_press_course_content_lesson', 'learn_press_update_user_lesson_start_time' );

function learn_press_get_profile_user() {
	global $wp;
	return !empty( $wp->query_vars['user'] ) ? get_user_by( 'login', $wp->query_vars['user'] ) : false;
}

/**
 * Add instructor registration button to register page and admin bar
 */
function learn_press_user_become_teacher_registration_form() {
	if ( LP()->settings->get( 'instructor_registration' ) != 'yes' ) {
		return;
	}
	?>
	<p>
		<label for="become_teacher">
			<input type="checkbox" name="become_teacher" id="become_teacher">
			<?php _e( 'Want to be an instructor?', 'learnpress' ) ?>
		</label>
	</p>
	<?php
}

add_action( 'register_form', 'learn_press_user_become_teacher_registration_form' );

/**
 * Process instructor registration while user register new account
 *
 * @param $user_id
 */
function learn_press_update_user_teacher_role( $user_id ) {
	if ( LP()->settings->get( 'instructor_registration' ) != 'yes' ) {
		return;
	}
	if ( !isset( $_POST['become_teacher'] ) ) {
		return;
	}
	$new_user = new WP_User( $user_id );
	$new_user->set_role( LP_TEACHER_ROLE );
}

add_action( 'user_register', 'learn_press_update_user_teacher_role', 10, 1 );


/**
 * @param array $fields
 * @param mixed $where
 *
 * @return mixed
 */
function learn_press_update_user_item_field( $fields, $where = false ) {
	global $wpdb;

	// Table fields
	$table_fields = array( 'user_id' => '%d', 'item_id' => '%d', 'ref_id' => '%d', 'start_time' => '%s', 'end_time' => '%s', 'item_type' => '%s', 'status' => '%s', 'ref_type' => '%s', 'parent_id' => '%d' );

	// Data and format
	$data        = array();
	$data_format = array();

	// Build data and data format
	foreach ( $fields as $field => $value ) {
		if ( !empty( $table_fields[$field] ) ) {
			$data[$field]  = $value;
			$data_format[] = $table_fields[$field];
		}
	}

	//
	if ( $where && empty( $where['user_id'] ) ) {
		$where['user_id'] = learn_press_get_current_user_id();
	}
	$where_format = array();
	/// Build where and where format
	if ( $where ) foreach ( $where as $field => $value ) {
		if ( !empty( $table_fields[$field] ) ) {
			$where_format[] = $table_fields[$field];
		}
	}
	$return = false;
	if ( $data ) {
		if ( $where ) {
			$return = $wpdb->update(
				$wpdb->prefix . 'learnpress_user_items',
				$data,
				$where,
				$data_format,
				$where_format
			);
		} else {
			if ( $wpdb->insert(
				$wpdb->prefix . 'learnpress_user_items',
				$data,
				$data_format
			)
			) {
				$return = $wpdb->insert_id;
			}
		}
	}
	return $return;
}

/**
 * Get user item row(s) from user items table by multiple WHERE conditional
 *
 * @param      $where
 * @param bool $single
 *
 * @return array|bool|null|object|void
 */
function learn_press_get_user_item( $where, $single = true ) {
	global $wpdb;

	// Table fields
	$table_fields = array( 'user_id' => '%d', 'item_id' => '%d', 'ref_id' => '%d', 'start_time' => '%s', 'end_time' => '%s', 'item_type' => '%s', 'status' => '%s', 'ref_type' => '%s', 'parent_id' => '%d' );

	$where_str = array();
	foreach ( $where as $field => $value ) {
		if ( !empty( $table_fields[$field] ) ) {
			$where_str[] = "{$field} = " . $table_fields[$field];
		}
	}
	$item = false;
	if ( $where_str ) {
		$query = $wpdb->prepare( "
			SELECT *
			FROM {$wpdb->prefix}learnpress_user_items
			WHERE " . join( ' AND ', $where_str ) . "
		", $where );
		if ( $single ) {
			$item = $wpdb->get_row( $query );
		} else {
			$item = $wpdb->get_results( $query );
		}
	}
	return $item;
}

/**
 * Get user item meta from user_itemmeta table
 *
 * @param      $user_item_id
 * @param      $meta_key
 * @param bool $single
 *
 * @return mixed
 */
function learn_press_get_user_item_meta( $user_item_id, $meta_key, $single = true ) {
	return get_metadata( 'learnpress_user_item', $user_item_id, $meta_key, $single );
}

/**
 * Add user item meta into table user_itemmeta
 *
 * @param        $user_item_id
 * @param        $meta_key
 * @param        $meta_value
 * @param string $prev_value
 *
 * @return false|int
 */
function learn_press_add_user_item_meta( $user_item_id, $meta_key, $meta_value, $prev_value = '' ) {
	return add_metadata( 'learnpress_user_item', $user_item_id, $meta_key, $meta_value, $prev_value );
}

/**
 * Update user item meta to table user_itemmeta
 *
 * @param        $user_item_id
 * @param        $meta_key
 * @param        $meta_value
 * @param string $prev_value
 *
 * @return bool|int
 */
function learn_press_update_user_item_meta( $user_item_id, $meta_key, $meta_value, $prev_value = '' ) {
	return update_metadata( 'learnpress_user_item', $user_item_id, $meta_key, $meta_value, $prev_value );
}

/**
 * Update field created_time after added user item meta
 *
 * @use updated_{meta_type}_meta hook
 *
 * @param $meta_id
 * @param $object_id
 * @param $meta_key
 * @param $_meta_value
 */
function _learn_press_update_created_time_user_item_meta( $meta_id, $object_id, $meta_key, $_meta_value ) {
	global $wpdb;
	$wpdb->update(
		$wpdb->learnpress_user_itemmeta,
		array( 'create_time' => current_time( 'mysql' ) ),
		array( 'meta_id' => $meta_id ),
		array( '%s' ),
		array( '%d' )
	);
}

///add_action( 'added_learnpress_user_item_meta', '_learn_press_update_created_time_user_item_meta', 10, 4 );

/**
 * Update field updated_time after updated user item meta
 *
 * @use updated_{meta_type}_meta hook
 *
 * @param $meta_id
 * @param $object_id
 * @param $meta_key
 * @param $_meta_value
 */
function _learn_press_update_updated_time_user_item_meta( $meta_id, $object_id, $meta_key, $_meta_value ) {
	global $wpdb;
	$wpdb->update(
		$wpdb->learnpress_user_itemmeta,
		array( 'update_time' => current_time( 'mysql' ) ),
		array( 'meta_id' => $meta_id ),
		array( '%s' ),
		array( '%d' )
	);
}

//add_action( 'updated_learnpress_user_item_meta', '_learn_press_update_updated_time_user_item_meta', 10, 4 );

/**
 * @param     $status
 * @param int $quiz_id
 * @param int $user_id
 * @param int $course_id
 *
 * @return bool|mixed
 */
function learn_press_user_has_quiz_status( $status, $quiz_id = 0, $user_id = 0, $course_id = 0 ) {
	$user = learn_press_get_user( $user_id );
	return $user->has_quiz_status( $status, $quiz_id, $course_id );
}

add_action( 'init', 'learn_press_user_update_user_info' );

function learn_press_user_update_user_info() {
	global $wp, $wpdb;
	$user             = learn_press_get_current_user();
	$user_id          = learn_press_get_current_user_id();
	$message_template = '<div class="learn-press-message %s">'
		. '<p>%s</p>'
		. '</div>';

	if ( !$user_id || is_admin() ) {
		return;
	}
	if ( !empty( $_POST ) && isset( $_POST['from'] ) && isset( $_POST['action'] ) && $_POST['from'] == 'profile' && $_POST['action'] == 'update' ) {
# - - - - - - - - - - - - - - - - - - - -
# CREATE SOME DIRECTORY
#
		$upload = wp_get_upload_dir();
		$ppdir  = $upload['basedir'] . DIRECTORY_SEPARATOR . 'learn-press-profile';
		if ( !is_dir( $ppdir ) ) {
			mkdir( $ppdir );
		}
		$upload_dir = $ppdir . DIRECTORY_SEPARATOR . $user_id;
		if ( !is_dir( $upload_dir ) ) {
			mkdir( $upload_dir );
		}
		$upload_dir_tmp = $upload_dir . DIRECTORY_SEPARATOR . 'tmp';
		if ( !is_dir( $upload_dir_tmp ) ) {
			mkdir( $upload_dir_tmp );
		}
		$lp_profile_url = $upload['baseurl'] . '/learn-press-profile/' . $user_id . '/';
#
# CREATE SOME DIRECTORY
# - - - - - - - - - - - - - - - - - - - -


# - - - - - - - - - - - - - - - - - - - -
# UPLOAD TEMP PICTURE PROFILE
#
		if ( isset( $_POST['sub_action'] ) && 'upload_avatar' === $_POST['sub_action'] && isset( $_FILES['image'] ) ) {
			$image_name = $_FILES['image']['name'];
			$image_tmp  = $_FILES['image']['tmp_name'];
			$image_size = intval( $_FILES['image']['size'] );
			$image_type = strtolower( $_FILES['image']['type'] );
			$filename   = strtolower( pathinfo( $image_name, PATHINFO_FILENAME ) );
			$file_ext   = strtolower( pathinfo( $image_name, PATHINFO_EXTENSION ) );

			if ( ( !empty( $_FILES["image"] ) ) && ( $_FILES['image']['error'] == 0 ) ) {
				$allowed_image_types = array( 'image/pjpeg' => "jpg", 'image/jpeg' => "jpg", 'image/jpg' => "jpg", 'image/png' => "png", 'image/x-png' => "png", 'image/gif' => "gif" );
				$mine_types          = array_keys( $allowed_image_types );
				$image_exts          = array_values( $allowed_image_types );
				$image_size_limit    = 2;
				if ( !in_array( $image_type, $mine_types ) ) {
					$_message = __( 'Only', 'learnpress' ) . ' <strong>' . implode( ',', $image_exts ) . '</strong> ' . __( 'images accepted for upload', 'learnpress' );
					$message  = sprintf( $message_template, 'error', $_message );
					$return   = array(
						'return'  => false,
						'message' => $message
					);
					learn_press_send_json( $return );
				}
				if ( $image_size > $image_size_limit * 1048576 ) {
					$message = __( 'Images must be under', 'learnpress' ) . ' ' . $image_size_limit . __( 'MB in size', 'learnpress' );
					$return  = array(
						'return'  => false,
						'message' => $message
					);
					learn_press_send_json( $return );
				}
			} else {
				$message = __( 'Please select an image for upload', 'learnpress' );
				$return  = array(
					'return'  => false,
					'message' => $message
				);
				learn_press_send_json( $return );
			}

			if ( isset( $_FILES['image']['name'] ) ) {
				// upload picture to tmp folder
				$path_image_tmp = $upload_dir_tmp . DIRECTORY_SEPARATOR . $filename . '.' . $file_ext;
				if ( file_exists( $path_image_tmp ) ) {
					$filename .= '1';
					$path_image_tmp = $upload_dir_tmp . DIRECTORY_SEPARATOR . $filename . '.' . $file_ext;
				}
				$uploaded = move_uploaded_file( $image_tmp, $path_image_tmp );
				chmod( $path_image_tmp, 0777 );
				if ( $uploaded ) {
					$editor3 = wp_get_image_editor( $path_image_tmp );
					if ( !is_wp_error( $editor3 ) ) {
						# Calculator new width height
						$size_current = $editor3->get_size();
						if ( $size_current['width'] < 250 || $size_current['width'] < 250 ) {
							$editor3->resize( 250, 250, true );
							$saved = $editor3->save();
						}
					}
				}

				$_message = $uploaded ? __( 'Image is uploaded success', 'learnpress' ) : __( 'Error on upload image', 'learnpress' );
				$message  = sprintf( $message_template, 'success', $_message );
				$return   = array(
					'return'  => $uploaded,
					'message' => $message
				);
				if ( $uploaded ) {
					$return['avatar_tmp']          = $lp_profile_url . 'tmp/' . $filename . '.' . $file_ext;
					$return['avatar_tmp_filename'] = $filename . '.' . $file_ext;
				}
				learn_press_send_json( $return );
			}
			exit();
		}
# 
# END OF UPLOAD TEMP PROFILE PICTURE
# - - - - - - - - - - - - - - - - - - - -

# - - - - - - - - - - - - - - - - - - - -
# CREATE PROFILE PICTURE & THUMBNAIL
#	
		if ( isset( $_POST['sub_action'] ) && 'crop_avatar' === $_POST['sub_action'] && isset( $_POST['avatar_filename'] ) ) {
			$avatar_filename = filter_input( INPUT_POST, 'avatar_filename', FILTER_SANITIZE_STRING );
			$avatar_filepath = $upload_dir . DIRECTORY_SEPARATOR . $avatar_filename;
			$editor          = wp_get_image_editor( $upload_dir_tmp . DIRECTORY_SEPARATOR . $avatar_filename );
			if ( is_wp_error( $editor ) ) {
				learn_press_add_message( __( 'Thumbnail of image profile not created', 'learnpress' ) );
			} else {
				# Calculator new width height
				$size_current = $editor->get_size();
				$zoom         = floatval( $_POST['zoom'] );
				$offset       = $_POST['offset'];
				$size_new     = array(
					'width'  => $size_current['width'] * $zoom,
					'height' => $size_current['height'] * $zoom
				);
				$editor->resize( $size_new['width'], $size_new['height'], true );
				$offset_x = max( intval( $offset['x'] ), - intval( $offset['x'] ) );
				$offset_y = max( intval( $offset['y'] ), - intval( $offset['y'] ) );
				$editor->crop( $offset_x, $offset_y, 248, 248 );
				$saved          = $editor->save( $upload_dir . DIRECTORY_SEPARATOR . $avatar_filename );
				$res            = array();
				$res['message'] = '';
				if ( is_wp_error( $saved ) ) {
					$_message               = __( 'Error on crop user picture profile ', 'learnpress' );
					$message                = sprintf( $message_template, 'error', $_message );
					$res['return']          = false;
					$res['message']         = $message;
					$res['avatar_filename'] = '';
					$res['avatar_url']      = '';
				} else {

					# - - - - - - - - - - - - - - - - - - - -
					# Create Thumbnai
					#
					if ( file_exists( $avatar_filepath ) ) {
						$editor2 = wp_get_image_editor( $avatar_filepath );
						if ( is_wp_error( $editor2 ) ) {
//								learn_press_add_message( __( 'Thumbnail of image profile not created', 'learnpress' ) );
							$_message = __( 'Thumbnail of image profile not created', 'learnpress' );
							$message  = sprintf( $message_template, 'error', $_message );
							$res['message'] .= $message;
						} else {
							$editor2->set_quality( 90 );
							$lp         = LP();
							$lp_setting = $lp->settings;
							$size       = $lp_setting->get( 'profile_picture_thumbnail_size' );
							if ( empty( $size ) ) {
								$size = array( 'width' => 150, 'height' => 150, 'crop' => 'yes' );
							}
							if ( isset( $size['crop'] ) && $size['crop'] == 'yes' ) {
								$size_width  = $size['width'];
								$size_height = $size['height'];
								$resized     = $editor2->resize( $size_width, $size_height, true );
								if ( is_wp_error( $resized ) ) {
//										learn_press_add_message( __( 'Thumbnail of image profile not created', 'learnpress' ) );
									$_message = __( 'Thumbnail of image profile not created', 'learnpress' );
									$message  = sprintf( $message_template, 'error', $_message );
									$res['message'] .= $message;
								} else {
									$dest_file = $editor2->generate_filename( 'thumb' );
									$saved     = $editor2->save( $dest_file );
									if ( is_wp_error( $saved ) ) {
//											learn_press_add_message( __( 'Thumbnail of image profile not created', 'learnpress' ) );
										$_message = __( 'Thumbnail of image profile not created', 'learnpress' );
										$message  = sprintf( $message_template, 'error', $_message );
										$res['message'] .= $message;
									}
								}
							}
						}
					}
					#
					# Create Thumbnai for Profile Picture
					# - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

					update_user_meta( $user->id, '_lp_profile_picture', $avatar_filename );
					update_user_meta( $user->id, '_lp_profile_picture_type', 'picture' );
					$_message      = __( 'Profile picture is changed', 'learnpress' );
					$message       = sprintf( $message_template, 'success', $_message );
					$res['return'] = true;
					$res['message'] .= $message;
					$res['avatar_filename'] = $avatar_filename;
					$res['avatar_url']      = $lp_profile_url . $avatar_filename;
				}
				learn_press_send_json( $res );
			}
			exit();
		}
#		
# CREATE PROFILE PICTURE & THUMBNAIL
# - - - - - - - - - - - - - - - - - - - -


# - - - - - - - - - - - - - - - - - - - -
# UPDATE USER INFO
#	
		$return      = array();
		$update_data = array(
			'ID'           => $user_id,
//			'user_url'     => filter_input( INPUT_POST, 'url', FILTER_SANITIZE_URL ),
//			'user_email'   => filter_input( INPUT_POST, 'email', FILTER_SANITIZE_EMAIL ),
			'first_name'   => filter_input( INPUT_POST, 'first_name', FILTER_SANITIZE_STRING ),
			'last_name'    => filter_input( INPUT_POST, 'last_name', FILTER_SANITIZE_STRING ),
			'display_name' => filter_input( INPUT_POST, 'display_name', FILTER_SANITIZE_STRING ),
			'nickname'     => filter_input( INPUT_POST, 'nickname', FILTER_SANITIZE_STRING ),
			'description'  => filter_input( INPUT_POST, 'description', FILTER_SANITIZE_STRING ),
		);
		# check and update pass word
		if ( !empty( $_POST['pass0'] ) && !empty( $_POST['pass1'] ) && !empty( $_POST['pass1'] ) ) {
			// check old pass
			$old_pass       = filter_input( INPUT_POST, 'pass0' );
			$check_old_pass = false;
			if ( !$old_pass ) {
				$check_old_pass = false;
			} else {
				$cuser = wp_get_current_user();
				require_once( ABSPATH . 'wp-includes/class-phpass.php' );
				$wp_hasher = new PasswordHash( 8, TRUE );
				if ( $wp_hasher->CheckPassword( $old_pass, $cuser->data->user_pass ) ) {
					$check_old_pass = true;
				}
			}
			if ( !$check_old_pass ) {
//				learn_press_add_message( __( 'Old password incorrect!', 'learnpress' ), 'error' );
				$_message               = __( 'Old password incorrect!', 'learnpress' );
				$message                = sprintf( $message_template, 'error', $_message );
				$return['return']       = false;
				$return['message']      = $message;
				$return['redirect_url'] = '';
				learn_press_send_json( $return );
				exit();
				return;
			} else {
				// check new pass
				$new_pass  = filter_input( INPUT_POST, 'pass1' );
				$new_pass2 = filter_input( INPUT_POST, 'pass2' );
				if ( $new_pass != $new_pass2 ) {
//					learn_press_add_message( __( 'Confirmation password incorrect!', 'learnpress' ), 'error' );
					$_message               = __( 'Confirmation password incorrect!', 'learnpress' );
					$message                = sprintf( $message_template, 'error', $_message );
					$return['return']       = false;
					$return['message']      = $message;
					$return['redirect_url'] = '';
					learn_press_send_json( $return );
					exit();
					return;
				} else {
					$update_data['user_pass'] = $new_pass;
				}
			}
		}

		$profile_picture_type = filter_input( INPUT_POST, 'profile_picture_type', FILTER_SANITIZE_STRING );
		update_user_meta( $user->id, '_lp_profile_picture_type', $profile_picture_type );
		$res = wp_update_user( $update_data );
		if ( $res ) {
//			learn_press_add_message( __( 'Your change is saved', 'learnpress' ) );
			$_message               = __( 'Your change is saved', 'learnpress' );
			$message                = sprintf( $message_template, 'success', $_message );
			$return['return']       = true;
			$return['message']      = $message;
			$return['redirect_url'] = '';
			learn_press_send_json( $return );
			exit();
		} else {
//			learn_press_add_message( __( 'Error on update your profile info', 'learnpress' ) );
			$_message               = __( 'Error on update your profile info', 'learnpress' );
			$message                = sprintf( $message_template, 'error', $_message );
			$return['return']       = false;
			$return['message']      = $message;
			$return['redirect_url'] = '';
			learn_press_send_json( $return );
			exit();
		}

		$current_url = learn_press_get_page_link( 'profile' ) . $user->user_login . '/edit';
		wp_redirect( $current_url );
		exit();
//		if ( !empty( $_POST['profile-nonce'] ) && wp_verify_nonce( $_POST['profile-nonce'], 'learn-press-user-profile-' . $user->id ) ) {
//			$current_url = learn_press_get_page_link( 'profile' ) . $user->user_login . '/edit';
//			wp_redirect( $current_url );
//			exit();
//		}
#
# UPDATE USER INFO
# - - - - - - - - - - - - - - - - - - - -


	}
}

if ( !function_exists( 'learn_press_pre_get_avatar_callback' ) ) {
	/**
	 * @param        $avatar
	 * @param string $id_or_email
	 * @param array  $size
	 * @param string $default
	 * @param string $alt
	 *
	 * @return string|void
	 */
	function learn_press_pre_get_avatar_callback( $avatar, $id_or_email = '', $size ) {
		if ( ( isset( $size['gravatar'] ) && $size['gravatar'] ) || ( $size['default'] && $size['force_default'] ) ) {
			return;
		}
		$user_id = 0;
		if ( !is_numeric( $id_or_email ) && is_string( $id_or_email ) ) {
			if ( $user = get_user_by( 'email', $id_or_email ) ) {
				$user_id = $user->ID;
			}
		} elseif ( is_numeric( $id_or_email ) ) {
			$user_id = $id_or_email;
		} elseif ( is_object( $id_or_email ) && isset( $id_or_email->user_id ) && $id_or_email->user_id ) {
			$user_id = $id_or_email->user_id;
		}
		$profile_picture_type = get_user_meta( $user_id, '_lp_profile_picture_type', true );
		$upload               = wp_upload_dir();
		$profile_picture      = get_user_meta( $user_id, '_lp_profile_picture', true );
		if ( !$profile_picture ) {
			return;
		}
		$user_profile_picture_dir = $upload['basedir'] . DIRECTORY_SEPARATOR . 'learn-press-profile' . DIRECTORY_SEPARATOR . $user_id . DIRECTORY_SEPARATOR;
		$user_profile_picture_url = $upload['baseurl'] . '/learn-press-profile/' . $user_id . '/';

		if ( $size === 'thumbnail' ) {
			$pi                    = pathinfo( $profile_picture );
			$profile_picture_thumb = $pi['filename'] . '-thumb' . '.' . $pi['extension'];
			if ( file_exists( $user_profile_picture_dir . $profile_picture_thumb ) ) {
				$profile_picture = $profile_picture_thumb;
			}
		}
		$profile_picture_src = $user_profile_picture_url . $profile_picture;
		if ( ( !isset( $size['gravatar'] ) || !isset( $size['gravatar'] ) && ( $size['gravatar'] ) )
			&& ( !$profile_picture_type || $profile_picture_type == 'gravatar' || !$profile_picture_src )
		) {
			return $avatar;
		}
		$lp           = LP();
		$lp_setting   = $lp->settings;
		$setting_size = $lp_setting->get( 'profile_picture_thumbnail_size' );

		$img_size = '';
		$height   = '';
		$width    = '';

		if ( !is_array( $size ) ) {
			if ( $size === 'thumbnail' ) {
				$img_size = '';
				$height   = $setting_size['height'];
				$width    = $setting_size['width'];
			} else {
				$height = 250;
				$width  = 250;
			}
		} else {
			$img_size = $size['size'];
			$height   = $size['height'];
			$width    = $size['width'];
		}
		$avatar = '<img alt="" src="' . esc_attr( $profile_picture_src ) . '" class="avatar avatar-' . $img_size . ' photo" height="' . $height . '" width="' . $width . '" />';
		return $avatar;
	}
}
add_filter( 'pre_get_avatar', 'learn_press_pre_get_avatar_callback', 1, 5 );


function learn_press_user_profile_picture_upload_dir( $args ) {
	$subdir         = '/learn-press-profile';
	$args['path']   = str_replace( $args['subdir'], $subdir, $args['path'] );
	$args['url']    = str_replace( $args['subdir'], $subdir, $args['url'] );
	$args['subdir'] = $subdir;
	return $args;
}

add_action( 'learn_press_before_purchase_course_handler', '_learn_press_before_purchase_course_handler', 10, 2 );
function _learn_press_before_purchase_course_handler( $course_id, $cart ) {
	// Redirect to login page if user is not logged in
	if ( !is_user_logged_in() ) {
		$return_url = add_query_arg( $_POST, get_the_permalink( $course_id ) );
		$return_url = apply_filters( 'learn_press_purchase_course_login_redirect_return_url', $return_url );
		$redirect   = apply_filters( 'learn_press_purchase_course_login_redirect', learn_press_get_login_url( $return_url ) );
		if ( $redirect !== false ) {
			learn_press_add_message( __( 'Você precisa estar logado para acessar ou comprar nossos treinamentos. Para criar sua conta, envie um e-mail para <strong>caio@ialtaperformance.com</strong>', 'learnpress' ) );

			if ( is_ajax() ) {
				learn_press_send_json(
					array(
						'redirect' => $redirect,
						'result'   => 'success'
					)
				);
			} else {
				wp_redirect( $redirect );
				exit();
			}
		}
	} else {
		$user     = learn_press_get_current_user();
		$redirect = false;
		if ( $user->has_finished_course( $course_id ) ) {
			learn_press_add_message( __( 'You have already finished course', 'learnpress' ) );
			$redirect = true;
		} elseif ( $user->has_purchased_course( $course_id ) ) {
			learn_press_add_message( __( 'You have already enrolled in this course', 'learnpress' ) );
			$redirect = true;
		}
		if ( $redirect ) {
			wp_redirect( get_the_permalink( $course_id ) );
			exit();
		}
	}
}

function learn_press_user_is( $role, $user_id = 0 ) {
	if ( !$user_id ) {
		$user = learn_press_get_current_user();
	} else {
		$user = learn_press_get_user( $user_id );
	}
	if ( $role == 'admin' ) {
		return $user->is_admin();
	}
	if ( $role == 'instructor' ) {
		return $user->is_instructor();
	}
	return $role;
}

function learn_press_profile_tab_endpoints_edit_profile( $endpoints ) {
	$endpoints['edit'] = 'edit';
	return $endpoints;
}

add_filter( 'learn_press_profile_tab_endpoints', 'learn_press_profile_tab_endpoints_edit_profile' );

function learn_press_profile_tab_edit_content( $current, $tab, $user ) {
	learn_press_get_template( 'profile/tabs/edit.php', array( 'user' => $user, 'current' => $current, 'tab' => $tab ) );
}

function _learn_press_redirect_logout_redirect() {
	if ( !is_admin() && $redirect = learn_press_get_page_link( 'profile' ) ) {
		wp_redirect( $redirect );
		exit();
	}
}

add_action( 'wp_logout', '_learn_press_redirect_logout_redirect' );

function learn_press_update_user_option( $name, $value, $id = 0 ) {
	if ( !$id ) {
		$id = get_current_user_id();
	}
	$key            = 'learnpress_user_options';
	$options        = get_user_option( $key, $id );
	$options[$name] = $value;
	update_user_option( $id, $key, $options, true );
}

function learn_press_delete_user_option( $name, $id = 0 ) {
	if ( !$id ) {
		$id = get_current_user_id();
	}
	$key     = 'learnpress_user_options';
	$options = get_user_option( $key, $id );
	if ( is_array( $options ) && array_key_exists( $name, $options ) ) {
		unset( $options[$name] );
		update_user_option( $id, $key, $options, true );
		return true;
	}
	return false;
}

function learn_press_get_user_option( $name, $id = 0 ) {
	if ( !$id ) {
		$id = get_current_user_id();
	}
	$key     = 'learnpress_user_options';
	$options = get_user_option( $key, $id );
	if ( is_array( $options ) && array_key_exists( $name, $options ) ) {
		return $options[$name];
	}
	return false;
}
