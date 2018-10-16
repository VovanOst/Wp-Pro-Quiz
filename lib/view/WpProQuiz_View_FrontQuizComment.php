<?php

/**
 * @property WpProQuiz_Model_StatisticHistory[] historyModel
 * @property WpProQuiz_Model_Form[] forms
 * @property bool avg
 * @property WpProQuiz_Model_StatisticRefModel statisticModel
 * @property WpProQuiz_Model_Comment CommentModel
 * @property string userName
 * @property array users
 */
class WpProQuiz_View_FrontQuizComment extends WpProQuiz_View_View {

	//private $_clozeTemp = array();
	//private $_assessmetTemp = array();

	//private $_buttonNames = array();

	public function show($preview = false) {
?>
<div style="margin-bottom: 30px; margin-top: 10px;" class="wpProQuiz_toplist"
     data-quiz_id="<?php echo $this->quiz->getId(); ?>">
	<?php if (!$this->inQuiz) { ?>
		<h2><?php _e('Leaderboard', 'wp-pro-quiz'); ?>: <?php echo $this->quiz->getName(); ?></h2>
	<?php } ?>
	<table class="wpProQuiz_toplistTable">
		<caption><?php printf(__('maximum of %s points', 'wp-pro-quiz'), $this->points); ?></caption>
		<thead>
		<tr>
			<th style="width: 40px;"><?php _e('Pos.', 'wp-pro-quiz'); ?></th>
			<th style="text-align: left !important;"><?php _e('Name', 'wp-pro-quiz'); ?></th>
			<th style="width: 140px;"><?php _e('Entered on', 'wp-pro-quiz'); ?></th>
			<th style="width: 60px;"><?php _e('Points', 'wp-pro-quiz'); ?></th>
			<th style="width: 75px;"><?php _e('Result', 'wp-pro-quiz'); ?></th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td colspan="5"><?php _e('Table is loading', 'wp-pro-quiz'); ?></td>
		</tr>
		<tr style="display: none;">
			<td colspan="5"><?php _e('No data available', 'wp-pro-quiz'); ?></td>
		</tr>
		<tr style="display: none;">
			<td></td>
			<td style="text-align: left !important;"></td>
			<td style=" color: rgb(124, 124, 124); font-size: x-small;"></td>
			<td></td>
			<td></td>
		</tr>
		</tbody>
	</table>
</div>
		<?php

	}

	public function  show1($preview=false)
	{
		$globalPoints = 0;
		$json = array();
		$catPoints = array();
		?>
		<div style="display: block;" class="wpProQuiz_quiz">
			<ol class="wpProQuiz_list">
				<?php
				$index = 0;
				foreach ($this->question as $question) {
					$index++;

					/* @var $answerArray WpProQuiz_Model_AnswerTypes[] */
					$answerArray = $question->getAnswerData();

					$globalPoints += $question->getPoints();

					$json[$question->getId()]['type'] = $question->getAnswerType();
					$json[$question->getId()]['id'] = (int)$question->getId();
					$json[$question->getId()]['catId'] = (int)$question->getCategoryId();

					if ($question->isAnswerPointsActivated() && $question->isAnswerPointsDiffModusActivated() && $question->isDisableCorrect()) {
						$json[$question->getId()]['disCorrect'] = (int)$question->isDisableCorrect();
					}

					if (!isset($catPoints[$question->getCategoryId()])) {
						$catPoints[$question->getCategoryId()] = 0;
					}

					$catPoints[$question->getCategoryId()] += $question->getPoints();

					if (!$question->isAnswerPointsActivated()) {
						$json[$question->getId()]['points'] = $question->getPoints();
						// 					$catPoints[$question->getCategoryId()] += $question->getPoints();
					}

					if ($question->isAnswerPointsActivated() && $question->isAnswerPointsDiffModusActivated()) {
						// 					$catPoints[$question->getCategoryId()] += $question->getPoints();
						$json[$question->getId()]['diffMode'] = 1;
					}

					?>
					<li class="wpProQuiz_listItem" style="display: list-item;">
						<div
							class="wpProQuiz_question_page" <?php $this->isDisplayNone($this->quiz->getQuizModus() != WpProQuiz_Model_Quiz::QUIZ_MODUS_SINGLE && !$this->quiz->isHideQuestionPositionOverview()); ?> >
							<?php printf(__('Question %s of %s', 'wp-pro-quiz'), '<span>' . $index . '</span>',
								'<span>' . '$questionCount' . '</span>'); ?>
						</div>
						<h5 style="<?php echo $this->quiz->isHideQuestionNumbering() ? 'display: none;' : 'display: inline-block;' ?>"
						    class="wpProQuiz_header">
							<span><?php echo $index; ?></span>. <?php _e('Question', 'wp-pro-quiz'); ?>
						</h5>

						<?php if ($this->quiz->isShowPoints()) { ?>
							<span style="font-weight: bold; float: right;"><?php printf(__('%d points', 'wp-pro-quiz'),
									$question->getPoints()); ?></span>
							<div style="clear: both;"></div>
						<?php } ?>

						<?php if ($question->getCategoryId() && $this->quiz->isShowCategory()) { ?>
							<div style="font-weight: bold; padding-top: 5px;">
								<?php printf(__('Category: %s', 'wp-pro-quiz'),
									esc_html($question->getCategoryName())); ?>
							</div>
						<?php } ?>
						<div class="wpProQuiz_question" style="margin: 10px 0 0 0;">
							<div class="wpProQuiz_question_text">
								<?php echo do_shortcode(apply_filters('comment_text', $question->getQuestion())); ?>
							</div>
							<?php if ($question->getAnswerType() === 'matrix_sort_answer') { ?>
								<div class="wpProQuiz_matrixSortString">
									<h5 class="wpProQuiz_header"><?php _e('Sort elements', 'wp-pro-quiz'); ?></h5>
									<ul class="wpProQuiz_sortStringList">
										<?php
										$matrix = array();
										foreach ($answerArray as $k => $v) {
											$matrix[$k][] = $k;

											foreach ($answerArray as $k2 => $v2) {
												if ($k != $k2) {
													if ($v->getAnswer() == $v2->getAnswer()) {
														$matrix[$k][] = $k2;
													} else {
														if ($v->getSortString() == $v2->getSortString()) {
															$matrix[$k][] = $k2;
														}
													}
												}
											}
										}

										foreach ($answerArray as $k => $v) {
											?>
											<li class="wpProQuiz_sortStringItem" data-pos="<?php echo $k; ?>"
											    data-correct="<?php echo implode(',', $matrix[$k]); ?>">
												<?php echo $v->isSortStringHtml() ? $v->getSortString() : esc_html($v->getSortString()); ?>
											</li>
										<?php } ?>
									</ul>
									<div style="clear: both;"></div>
								</div>
							<?php } ?>
							<ul class="wpProQuiz_questionList" data-question_id="<?php echo $question->getId(); ?>"
							    data-type="<?php echo $question->getAnswerType(); ?>">
								<?php
								$answer_index = 0;

								foreach ($answerArray as $v) {
									$answer_text = $v->isHtml() ? $v->getAnswer() : esc_html($v->getAnswer());

									if ($answer_text == '') {
										continue;
									}

									if ($question->isAnswerPointsActivated()) {
										$json[$question->getId()]['points'][] = $v->getPoints();

										// 								if(!$question->isAnswerPointsDiffModusActivated())
										// 									$catPoints[$question->getCategoryId()] += $question->getPoints();
									}

									?>

									<li class="wpProQuiz_questionListItem" data-pos="<?php echo $answer_index; ?>">

										<?php if ($question->getAnswerType() === 'single' || $question->getAnswerType() === 'multiple') { ?>
											<?php $json[$question->getId()]['correct'][] = (int)$v->isCorrect(); ?>
											<span <?php echo $this->quiz->isNumberedAnswer() ? '' : 'style="display:none;"' ?>></span>
											<label>
												<input class="wpProQuiz_questionInput"
												       type="<?php echo $question->getAnswerType() === 'single' ? 'radio' : 'checkbox'; ?>"
												       name="question_<?php echo $this->quiz->getId(); ?>_<?php echo $question->getId(); ?>"
												       value="<?php echo($answer_index + 1); ?>"> <?php echo $answer_text; ?>
											</label>

										<?php } else {
											if ($question->getAnswerType() === 'sort_answer') { ?>
												<?php $json[$question->getId()]['correct'][] = (int)$answer_index; ?>
												<div class="wpProQuiz_sortable">
													<?php echo $answer_text; ?>
												</div>
											<?php } else {
												if ($question->getAnswerType() === 'free_answer') { ?>
													<?php $json[$question->getId()]['correct'] = $this->getFreeCorrect($v); ?>
													<label>
														<input class="wpProQuiz_questionInput" type="text"
														       name="question_<?php echo $this->quiz->getId(); ?>_<?php echo $question->getId(); ?>"
														       style="width: 300px;">
													</label>
												<?php } else {
													if ($question->getAnswerType() === 'matrix_sort_answer') { ?>
														<?php
														$json[$question->getId()]['correct'][] = (int)$answer_index;
														$msacwValue = $question->getMatrixSortAnswerCriteriaWidth() > 0 ? $question->getMatrixSortAnswerCriteriaWidth() : 20;
														?>
														<table>
															<tbody>
															<tr class="wpProQuiz_mextrixTr">
																<td width="<?php echo $msacwValue; ?>%">
																	<div
																		class="wpProQuiz_maxtrixSortText"><?php echo $answer_text; ?></div>
																</td>
																<td width="<?php echo 100 - $msacwValue; ?>%">
																	<ul class="wpProQuiz_maxtrixSortCriterion"></ul>
																</td>
															</tr>
															</tbody>
														</table>

													<?php } else {
														if ($question->getAnswerType() === 'cloze_answer') {
															$clozeData = $this->fetchCloze($v->getAnswer());

															$this->_clozeTemp = $clozeData['data'];

															$json[$question->getId()]['correct'] = $clozeData['correct'];

															if ($question->isAnswerPointsActivated()) {
																$json[$question->getId()]['points'] = $clozeData['points'];
															}

															$cloze = do_shortcode(apply_filters('comment_text',
																$clozeData['replace']));
															$cloze = $clozeData['replace'];

															echo preg_replace_callback('#@@wpProQuizCloze@@#im',
																array($this, 'clozeCallback'), $cloze);
														} else {
															if ($question->getAnswerType() === 'assessment_answer') {
																$assessmentData = $this->fetchAssessment($v->getAnswer(),
																	$this->quiz->getId(), $question->getId());

																$assessment = do_shortcode(apply_filters('comment_text',
																	$assessmentData['replace']));

																echo preg_replace_callback('#@@wpProQuizAssessment@@#im',
																	array($this, 'assessmentCallback'), $assessment);

															}
														}
													}
												}
											}
										} ?>
									</li>
									<?php
									$answer_index++;
								}
								?>
							</ul>
						</div>
						<?php if (!$this->quiz->isHideAnswerMessageBox()) { ?>
							<div class="wpProQuiz_response" style="display: none;">
								<div style="display: none;" class="wpProQuiz_correct">
									<?php if ($question->isShowPointsInBox() && $question->isAnswerPointsActivated()) { ?>
										<div>
									<span style="float: left;" class="wpProQuiz_respone_span">
										<?php _e('Correct', 'wp-pro-quiz'); ?>
									</span>
											<span
												style="float: right;"><?php echo $question->getPoints() . ' / ' . $question->getPoints(); ?><?php _e('Points',
													'wp-pro-quiz'); ?></span>

											<div style="clear: both;"></div>
										</div>
									<?php } else { ?>
										<span class="wpProQuiz_respone_span">
									<?php _e('Correct', 'wp-pro-quiz'); ?>
								</span><br>
									<?php }
									$_correctMsg = trim(do_shortcode(apply_filters('comment_text',
										$question->getCorrectMsg())));

									if (strpos($_correctMsg, '<p') === 0) {
										echo $_correctMsg;
									} else {
										echo '<p>', $_correctMsg, '</p>';
									}
									?>
								</div>
								<div style="display: none;" class="wpProQuiz_incorrect">
									<?php if ($question->isShowPointsInBox() && $question->isAnswerPointsActivated()) { ?>
										<div>
									<span style="float: left;" class="wpProQuiz_respone_span">
										<?php _e('Incorrect', 'wp-pro-quiz'); ?>
									</span>
											<span style="float: right;"><span
													class="wpProQuiz_responsePoints"></span> / <?php echo $question->getPoints(); ?> <?php _e('Points',
													'wp-pro-quiz'); ?></span>

											<div style="clear: both;"></div>
										</div>
									<?php } else { ?>
										<span class="wpProQuiz_respone_span">
									<?php _e('Incorrect', 'wp-pro-quiz'); ?>
								</span><br>
									<?php }

									if ($question->isCorrectSameText()) {
										$_incorrectMsg = do_shortcode(apply_filters('comment_text',
											$question->getCorrectMsg()));
									} else {
										$_incorrectMsg = do_shortcode(apply_filters('comment_text',
											$question->getIncorrectMsg()));
									}

									if (strpos($_incorrectMsg, '<p') === 0) {
										echo $_incorrectMsg;
									} else {
										echo '<p>', $_incorrectMsg, '</p>';
									}

									?>
								</div>
							</div>
						<?php } ?>

						<?php if ($question->isTipEnabled()) { ?>
							<div class="wpProQuiz_tipp" style="display: none; position: relative;">
								<div>
									<h5 style="margin: 0 0 10px;" class="wpProQuiz_header"><?php _e('Hint',
											'wp-pro-quiz'); ?></h5>
									<?php echo do_shortcode(apply_filters('comment_text', $question->getTipMsg())); ?>
								</div>
							</div>
						<?php } ?>

						<?php if ($this->quiz->getQuizModus() == WpProQuiz_Model_Quiz::QUIZ_MODUS_CHECK && !$this->quiz->isSkipQuestionDisabled() && $this->quiz->isShowReviewQuestion()) { ?>
							<input type="button" name="skip" value="<?php _e('Skip question', 'wp-pro-quiz'); ?>"
							       class="wpProQuiz_button wpProQuiz_QuestionButton"
							       style="float: left; margin-right: 10px !important;">
						<?php } ?>
						<input type="button" name="back" value="<?php _e('Back', 'wp-pro-quiz'); ?>"
						       class="wpProQuiz_button wpProQuiz_QuestionButton"
						       style="float: left !important; margin-right: 10px !important; display: none;">
						<?php if ($question->isTipEnabled()) { ?>
							<input type="button" name="tip" value="<?php _e('Hint', 'wp-pro-quiz'); ?>"
							       class="wpProQuiz_button wpProQuiz_QuestionButton wpProQuiz_TipButton"
							       style="float: left !important; display: inline-block; margin-right: 10px !important;">
						<?php } ?>
						<input type="button" name="check" value="<?php _e('Check', 'wp-pro-quiz'); ?>"
						       class="wpProQuiz_button wpProQuiz_QuestionButton"
						       style="float: right !important; margin-right: 10px !important; display: none;">
						<input type="button" name="next" value="<?php _e('Next', 'wp-pro-quiz'); ?>"
						       class="wpProQuiz_button wpProQuiz_QuestionButton" style="float: right; display: none;">

						<div style="clear: both;"></div>

						<?php if ($this->quiz->getQuizModus() == WpProQuiz_Model_Quiz::QUIZ_MODUS_SINGLE) { ?>
							<div style="margin-bottom: 20px;"></div>
						<?php } ?>

					</li>

				<?php } ?>
			</ol>
			<?php if ($this->quiz->getQuizModus() == WpProQuiz_Model_Quiz::QUIZ_MODUS_SINGLE) { ?>
				<div>
					<input type="button" name="wpProQuiz_pageLeft"
					       data-text="<?php echo esc_attr(__('Page %d', 'wp-pro-quiz')); ?>"
					       style="float: left; display: none;" class="wpProQuiz_button wpProQuiz_QuestionButton">
					<input type="button" name="wpProQuiz_pageRight"
					       data-text="<?php echo esc_attr(__('Page %d', 'wp-pro-quiz')); ?>"
					       style="float: right; display: none;" class="wpProQuiz_button wpProQuiz_QuestionButton">

					<?php if ($this->quiz->isShowReviewQuestion() && !$this->quiz->isQuizSummaryHide()) { ?>
						<input type="button" name="checkSingle"
						       value="<?php echo $this->_buttonNames['quiz_summary']; ?>"
						       class="wpProQuiz_button wpProQuiz_QuestionButton" style="float: right;">
					<?php } else { ?>
						<input type="button" name="checkSingle"
						       value="<?php echo $this->_buttonNames['finish_quiz']; ?>"
						       class="wpProQuiz_button wpProQuiz_QuestionButton" style="float: right;">
					<?php } ?>

					<div style="clear: both;"></div>
				</div>
			<?php } ?>
		</div>
		<?php

		return array('globalPoints' => $globalPoints, 'json' => $json, 'catPoints' => $catPoints);
	}

	public function show2()
	{
		?>

		<table class="wp-list-table widefat">
			<thead>
			<tr>
				<th scope="col"><?php _e('Username', 'wp-pro-quiz'); ?></th>

				<?php foreach ($this->forms as $form) {
					/* @var $form WpProQuiz_Model_Form */
					if ($form->isShowInStatistic()) {
						echo '<th scope="col">' . $form->getFieldname() . '</th>';
					}
				} ?>

				<th scope="col" style="width: 200px;"><?php _e('Date', 'wp-pro-quiz'); ?></th>
				<th scope="col" style="width: 100px;"><?php _e('Correct', 'wp-pro-quiz'); ?></th>
				<th scope="col" style="width: 100px;"><?php _e('Incorrect', 'wp-pro-quiz'); ?></th>
				<th scope="col" style="width: 100px;"><?php _e('Solved', 'wp-pro-quiz'); ?></th>
				<th scope="col" style="width: 100px;"><?php _e('Points', 'wp-pro-quiz'); ?></th>
				<th scope="col" style="width: 60px;"><?php _e('Results', 'wp-pro-quiz'); ?></th>
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
							<a href="#" class="user_statistic"
							   data-ref_id="<?php echo $model->getStatisticRefId(); ?>"><?php echo $model->getUserName(); ?></a>

							<div class="row-actions">
							<span>
								<a style="color: red;" class="wpProQuiz_delete" href="#"><?php _e('Delete',
										'wp-pro-quiz'); ?></a>
							</span>
							</div>

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
