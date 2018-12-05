<?php

/**
 * @property WpProQuiz_Model_StatisticHistory[] historyModel *
 * @property bool avg
 * @property WpProQuiz_Model_StatisticRefModel statisticModel
 * @property WpProQuiz_Model_Comment CommentModel
 * @property string userName
 * @property array users
 */
class WpProQuiz_View_FrontStatisticsUser extends WpProQuiz_View_View {

	//private $_clozeTemp = array();
	//private $_assessmetTemp = array();

	//private $_buttonNames = array();



	public function show2()
	{
		?>
		<table class="wp-list-table widefat">
			<thead>
			<tr>
				<th scope="col"><?php _e('Тест', 'wp-pro-quiz'); ?></th>



				<th scope="col" style="width: 200px;"><?php _e('Date', 'wp-pro-quiz'); ?></th>
				<th scope="col" style="width: 100px;"><?php _e('Correct', 'wp-pro-quiz'); ?></th>
				<th scope="col" style="width: 100px;"><?php _e('Incorrect', 'wp-pro-quiz'); ?></th>
				<th scope="col" style="width: 100px;"><?php _e('Solved', 'wp-pro-quiz'); ?></th>
				<th scope="col" style="width: 100px;"><?php _e('Points', 'wp-pro-quiz'); ?></th>
				<th scope="col" style="width: 60px;"><?php _e('Результат', 'wp-pro-quiz'); ?></th>
			</tr>
			</thead>
			<tbody id="wpProQuiz_statistics_form_data">
			<?php if (!count($this->historyModel)) { ?>
				<tr>
					<td colspan="6"
					    style="text-align: center; font-weight: bold; padding: 10px;"><?php _e('No data available',
							'wp-pro-quiz'); ?></td>
				</tr>
			<?php } else { ?>
				<?php foreach ($this->historyModel as $model) {
					/* @var $model WpProQuiz_Model_StatisticHistory */ ?>
					<tr>
						<th>
							<a href="<?php home_url()?>test-details/?userRefid=<?php echo $model->getStatisticRefId(); ?>&quizId=<?php echo $model->getQuizId();?>" class="fancybox-iframe"
							   data-ref_id="<?php echo $model->getStatisticRefId(); ?>"><?php echo $model->getQuizName(); ?></a>

						</th>
						<?php foreach ($model->getFormOverview() as $form) {
							echo '<th>' . esc_html($form) . '</th>';
						} ?>
						<th><?php echo $model->getFormatTime(); ?></th>
						<th style="color: green;"><?php echo $model->getFormatCorrect(); ?></th>
						<th style="color: red;"><?php echo $model->getFormatIncorrect(); ?></th>
						<th><?php echo $model->getSolvedCount() < 0 ? '---' : sprintf(__('%d of %d', 'wp-pro-quiz'),
								$model->getSolvedCount(),
								$model->getCorrectCount() + $model->getIncorrectCount()); ?></th>
						<th><?php echo $model->getPoints(); ?></th>
						<th style="font-weight: bold;"><?php echo $model->getResult(); ?>%</th>
					</tr>
				<?php }
			} ?>
			</tbody>
		</table>

		<?php
	}

	private function fetchCloze($answer_text)
	{
		preg_match_all('#\{(.*?)(?:\|(\d+))?(?:[\s]+)?\}#im', $answer_text, $matches, PREG_SET_ORDER);

		$data = array();

		foreach ($matches as $k => $v) {
			$text = $v[1];
			$points = !empty($v[2]) ? (int)$v[2] : 1;
			$rowText = $multiTextData = array();
			$len = array();

			if (preg_match_all('#\[(.*?)\]#im', $text, $multiTextMatches)) {
				foreach ($multiTextMatches[1] as $multiText) {
					$x = mb_strtolower(trim(html_entity_decode($multiText, ENT_QUOTES)));

					$len[] = strlen($x);
					$multiTextData[] = $x;
					$rowText[] = $multiText;
				}
			} else {
				$x = mb_strtolower(trim(html_entity_decode($text, ENT_QUOTES)));

				$len[] = strlen($x);
				$multiTextData[] = $x;
				$rowText[] = $text;
			}

			$a = '<span class="wpProQuiz_cloze"><input data-wordlen="' . max($len) . '" type="text" value=""> ';
			$a .= '<span class="wpProQuiz_clozeCorrect" style="display: none;">(' . implode(', ',
					$rowText) . ')</span></span>';

			$data['correct'][] = $multiTextData;
			$data['points'][] = $points;
			$data['data'][] = $a;
		}

		$data['replace'] = preg_replace('#\{(.*?)(?:\|(\d+))?(?:[\s]+)?\}#im', '@@wpProQuizCloze@@', $answer_text);

		return $data;
	}

	private function fetchAssessment($answerText, $quizId, $questionId)
	{
		preg_match_all('#\{(.*?)\}#im', $answerText, $matches);

		$this->_assessmetTemp = array();
		$data = array();

		for ($i = 0, $ci = count($matches[1]); $i < $ci; $i++) {
			$match = $matches[1][$i];

			preg_match_all('#\[([^\|\]]+)(?:\|(\d+))?\]#im', $match, $ms);

			$a = '';

			for ($j = 0, $cj = count($ms[1]); $j < $cj; $j++) {
				$v = $ms[1][$j];

				$a .= '<label>
					<input type="radio" value="' . ($j + 1) . '" name="question_' . $quizId . '_' . $questionId . '_' . $i . '" class="wpProQuiz_questionInput" data-index="' . $i . '">
					' . $v . '
				</label>';

			}

			$this->_assessmetTemp[] = $a;
		}

		$data['replace'] = preg_replace('#\{(.*?)\}#im', '@@wpProQuizAssessment@@', $answerText);

		return $data;
	}

	private function getFreeCorrect($data)
	{
		$t = str_replace("\r\n", "\n", strtolower($data->getAnswer()));
		$t = str_replace("\r", "\n", $t);
		$t = explode("\n", $t);

		return array_values(array_filter(array_map('trim', $t), array($this, 'removeEmptyElements')));
	}

	private function showHistory()
	{
		?>
		<div id="wpProQuiz_tabHistory" class="wpProQuiz_tabContent" style="display: block;">

			<div id="poststuff">
				<div class="postbox">
					<h3 class="hndle"><?php _e('Filter', 'wp-pro-quiz'); ?></h3>

					<div class="inside">
						<ul>
							<li>
								<label>
									<?php _e('Which users should be displayed:', 'wp-pro-quiz'); ?>
									<select id="wpProQuiz_historyUser">
										<optgroup label="<?php _e('special filter', 'wp-pro-quiz'); ?>">
											<option value="-1" selected="selected"><?php _e('all users',
													'wp-pro-quiz'); ?></option>
											<option value="-2"><?php _e('only registered users',
													'wp-pro-quiz'); ?></option>
											<option value="-3"><?php _e('only anonymous users',
													'wp-pro-quiz'); ?></option>
										</optgroup>

										<optgroup label="<?php _e('User', 'wp-pro-quiz'); ?>">
											<?php foreach ($this->users as $user) {
												if ($user->ID == 0) {
													continue;
												}

												echo '<option value="', $user->ID, '">', $user->user_login, ' (', $user->display_name, ')</option>';
											} ?>
										</optgroup>
									</select>
								</label>
							</li>
							<li>
								<label>
									<?php _e('How many entries should be shown on one page:', 'wp-pro-quiz'); ?>
									<select id="wpProQuiz_historyPageLimit">
										<option>1</option>
										<option selected="selected">10</option>
										<option>50</option>
										<option>100</option>
										<option>500</option>
										<option>1000</option>
									</select>
								</label>
							</li>
							<li>
								<?php
								$dateVon = '<input type="text" id="datepickerFrom">';
								$dateBis = '<input type="text" id="datepickerTo">';

								printf(__('Search to date limit from %s to %s', 'wp-pro-quiz'), $dateVon, $dateBis);
								?>
							</li>
							<li>
								<input type="button" value="<?php _e('Filter', 'wp-pro-quiz'); ?>"
								       class="button-secondary" id="filter">
							</li>
						</ul>
					</div>
				</div>
			</div>

			<div id="wpProQuiz_loadDataHistory" class="wpProQuiz_blueBox"
			     style="background-color: #F8F5A8; display: none;">
				<img alt="load"
				     src="data:image/gif;base64,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">
				<?php _e('Loading', 'wp-pro-quiz'); ?>
			</div>

			<div id="wpProQuiz_historyLoadContext"></div>

			<div style="margin-top: 10px;">

				<div style="float: left;" id="historyNavigation">
					<input style="font-weight: bold;" class="button-secondary navigationLeft" value="&lt;"
					       type="button">
					<select class="navigationCurrentPage">
						<option value="1">1</option>
					</select>
					<input style="font-weight: bold;" class="button-secondary navigationRight" value="&gt;"
					       type="button">
				</div>

				<div style="float: right;">
					<a class="button-secondary wpProQuiz_update" href="#"><?php _e('Refresh', 'wp-pro-quiz'); ?></a>
					<?php if (current_user_can('wpProQuiz_reset_statistics')) { ?>
						<a class="button-secondary wpProQuiz_resetComplete" href="#"><?php _e('Reset entire statistic',
								'wp-pro-quiz'); ?></a>
					<?php } ?>
				</div>

				<div style="clear: both;"></div>
			</div>

		</div>
		<?php
	}

	private function showModalWindow()
	{
		?>

		<div id="wpProQuiz_user_overlay" style="display: none;">
			<div class="wpProQuiz_modal_window" style="padding: 20px; overflow: scroll;">
				<input type="button" value="<?php _e('Close'); ?>" class="button-primary"
				       style=" position: fixed; top: 48px; right: 59px; z-index: 160001;" id="wpProQuiz_overlay_close">

				<div id="wpProQuiz_user_content" style="margin-top: 20px;"></div>

				<div id="wpProQuiz_loadUserData" class="wpProQuiz_blueBox"
				     style="background-color: #F8F5A8; display: none; margin: 50px;">
					<img alt="load"
					     src="data:image/gif;base64,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">
					<?php _e('Loading', 'wp-pro-quiz'); ?>
				</div>
			</div>
			<div class="wpProQuiz_modal_backdrop"></div>
		</div>

		<?php
	}

	private function showTabOverview()
	{
		?>
		<div id="wpProQuiz_tabOverview" class="wpProQuiz_tabContent" style="display: none;">
			<div id="poststuff">
				<div class="postbox">
					<h3 class="hndle"><?php _e('Filter', 'wp-pro-quiz'); ?></h3>

					<div class="inside">
						<ul>
							<li>
								<label>
									<?php _e('Show only users, who solved the quiz:', 'wp-pro-quiz'); ?>
									<input type="checkbox" value="1" id="wpProQuiz_overviewOnlyCompleted">
								</label>
							</li>
							<li>
								<label>
									<?php _e('How many entries should be shown on one page:', 'wp-pro-quiz'); ?>
									<select id="wpProQuiz_overviewPageLimit">
										<option>1</option>
										<option>4</option>
										<option selected="selected">50</option>
										<option>100</option>
										<option>500</option>
										<option>1000</option>
									</select>
								</label>
							</li>
							<li>
								<input type="button" value="<?php _e('Filter', 'wp-pro-quiz'); ?>"
								       class="button-secondary" id="overviewFilter">
							</li>
						</ul>
					</div>
				</div>
			</div>

			<div id="wpProQuiz_loadDataOverview" class="wpProQuiz_blueBox"
			     style="background-color: #F8F5A8; display: none;">
				<img alt="load"
				     src="data:image/gif;base64,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">
				<?php _e('Loading', 'wp-pro-quiz'); ?>
			</div>

			<div id="wpProQuiz_overviewLoadContext"></div>

			<div style="margin-top: 10px;">

				<div style="float: left;" id="overviewNavigation">
					<input style="font-weight: bold;" class="button-secondary navigationLeft" value="&lt;"
					       type="button">
					<select class="navigationCurrentPage">
						<option value="1">1</option>
					</select>
					<input style="font-weight: bold;" class="button-secondary navigationRight" value="&gt;"
					       type="button">
				</div>

				<div style="float: right;">
					<a class="button-secondary wpProQuiz_update" href="#"><?php _e('Refresh', 'wp-pro-quiz'); ?></a>
					<?php if (current_user_can('wpProQuiz_reset_statistics')) { ?>
						<a class="button-secondary wpProQuiz_resetComplete" href="#"><?php _e('Reset entire statistic',
								'wp-pro-quiz'); ?></a>
					<?php } ?>
				</div>

				<div style="clear: both;"></div>
			</div>

		</div>
		<?php
	}

}
