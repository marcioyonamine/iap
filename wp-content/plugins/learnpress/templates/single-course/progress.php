<?php
/**
 * @author        ThimPress
 * @package       LearnPress/Templates
 * @version       2.0.4
 */

defined( 'ABSPATH' ) || exit();

$course = LP()->global['course'];
$user   = learn_press_get_current_user();
if ( !$course ) {
	return;
}
$status = $user->get( 'course-status', $course->id );
if ( !$status || !$user->has_purchased_course( $course->id ) ) {
	return;
}
$force             = isset( $force ) ? $force : false;
$num_of_decimal    = 0;
$result            = $course->evaluate_course_results( null, $force );
$current           = absint( $result );
$passing_condition = round( $course->passing_condition, $num_of_decimal );
$passed            = $current >= $passing_condition;
$heading           = apply_filters( 'learn_press_course_progress_heading', $status == 'finished' ? __( 'Your results', 'learnpress' ) : __( 'Learning progress', 'learnpress' ) );
$course_items      = sizeof( $course->get_curriculum_items() );
$completed_items   = $course->count_completed_items();
$course_results    = $course->evaluate_course_results();
?>
<div class="learn-press-course-results-progress">
	<div class="items-progress">
		<?php 
			//$user_info = get_userdata(1);
	      //echo 'Olá ' . $user_info->user_login . "<br>";
	      //echo 'User roles: ' . implode(', ', $user_info->roles) . "\n";
	      //echo 'Seu ID de usuário é : ' . $user_info->ID . "<br>";
		  //echo 'Seu e-mail cadastrado é: ' . $user_info->user_email . "<br>";
		?>
		<?php if ( $heading !== false ): ?>
			<h4 class="lp-course-progress-heading"><?php echo esc_html_e( 'Progresso', 'learnpress' ); ?></h4>
		<?php endif; ?>
		<span class="number"><?php printf( __( '%d de %d aulas', 'learnpress' ), $completed_items, $course_items ); ?></span>
		<div class="lp-course-progress">
			<div class="lp-progress-bar">
				<div class="lp-progress-value"
					 style="width: <?php echo $course_items ? absint( $completed_items / $course_items * 100 ) : '0'; ?>%;">
				</div>
			</div>
		</div>

	</div>
	<div class="course-progress">
		<h4 class="lp-course-progress-heading">
			<?php esc_html_e( 'Course results', 'learnpress' ); ?>
			<?php
			//if ( $course->is_evaluation( 'evaluate_final_quiz' ) ) {
				//$tooltip = __( "Evaluated by results of final quiz", 'learnpress' );
			//} elseif ( $course->is_evaluation( 'evaluate_quizzes' ) ) {
				//$tooltip = __( "Evaluated by average results of quizzes", 'learnpress' );
			//} else {
				//$tooltip = __( "Evaluated by items completed", 'learnpress' );
			//}
			?>
			<!--<span class="learn-press-tooltip" data-content="<?php echo esc_html( $tooltip ); ?>"></span>-->
		</h4>
		<div>
			<span class="number"><?php echo $current ?></span><span class="percentage-sign">%</span>
		</div>
		<div class="lp-course-progress<?php echo $passed ? ' passed' : ''; ?>" data-value="<?php echo $current; ?>"
			 data-passing-condition="<?php echo $passing_condition; ?>">
			<div class="lp-progress-bar">
				<div class="lp-progress-value" style="width: <?php echo $current; ?>%;">
				</div>
			</div>
			<div class="lp-passing-conditional"
				 data-content="<?php printf( esc_html__( 'Passing condition: %s%%', 'learnpress' ), $passing_condition ); ?>"
				 style="left: <?php echo $passing_condition; ?>%;">
			</div>
		</div>
	</div>
	<?php /*if ($user->has_enrolled_course($course->id)): ?>
        <?php learn_press_get_template('single-course/buttons.php'); ?>
    <?php endif;*/ ?>
</div>