<?php

class WpProQuiz_Controller_Front
{

    /**
     * @var WpProQuiz_Model_GlobalSettings
     */
    private $_settings = null;

    public function __construct()
    {
        $this->loadSettings();

        add_action('wp_enqueue_scripts', array($this, 'loadDefaultScripts'));
        add_shortcode('WpProQuiz', array($this, 'shortcode'));
        add_shortcode('WpProQuiz_toplist', array($this, 'shortcodeToplist'));
	    add_shortcode('WpProQuiz_StatisticsUser', array($this, 'shortcodeStatisticsUser'));
	    add_shortcode('WpProQuiz_StatisticsUserDetails', array($this, 'shortcodeStatisticsUserDetails'));
    }

    public function loadDefaultScripts()
    {
        wp_enqueue_script('jquery');

        $data = array(
            'src' => plugins_url('css/wpProQuiz_front' . (WPPROQUIZ_DEV ? '' : '.min') . '.css', WPPROQUIZ_FILE),
            'deps' => array(),
            'ver' => WPPROQUIZ_VERSION,
        );

        $data = apply_filters('wpProQuiz_front_style', $data);

        wp_enqueue_style('wpProQuiz_front_style', $data['src'], $data['deps'], $data['ver']);

        if ($this->_settings->isJsLoadInHead()) {
            $this->loadJsScripts(false, true, true);
        }
    }

    private function loadJsScripts($footer = true, $quiz = true, $toplist = false)
    {
        if ($quiz) {
            wp_enqueue_script(
                'wpProQuiz_front_javascript',
                plugins_url('js/wpProQuiz_front' . (WPPROQUIZ_DEV ? '' : '.min') . '.js', WPPROQUIZ_FILE),
                array('jquery-ui-sortable'),
                WPPROQUIZ_VERSION,
                $footer
            );

            wp_localize_script('wpProQuiz_front_javascript', 'WpProQuizGlobal', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'loadData' => __('Loading', 'wp-pro-quiz'),
                'questionNotSolved' => __('You must answer this question.', 'wp-pro-quiz'),
                'questionsNotSolved' => __('You must answer all questions before you can completed the quiz.',
                    'wp-pro-quiz'),
                'fieldsNotFilled' => __('All fields have to be filled.', 'wp-pro-quiz')
            ));
        }

        if ($toplist) {
            wp_enqueue_script(
                'wpProQuiz_front_javascript_toplist',
                plugins_url('js/wpProQuiz_toplist' . (WPPROQUIZ_DEV ? '' : '.min') . '.js', WPPROQUIZ_FILE),
                array('jquery-ui-sortable'),
                WPPROQUIZ_VERSION,
                $footer
            );

            if (!wp_script_is('wpProQuiz_front_javascript')) {
                wp_localize_script('wpProQuiz_front_javascript_toplist', 'WpProQuizGlobal', array(
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'loadData' => __('Loading', 'wp-pro-quiz'),
                    'questionNotSolved' => __('You must answer this question.', 'wp-pro-quiz'),
                    'questionsNotSolved' => __('You must answer all questions before you can completed the quiz.',
                        'wp-pro-quiz'),
                    'fieldsNotFilled' => __('All fields have to be filled.', 'wp-pro-quiz')
                ));
            }
        }

        if (!$this->_settings->isTouchLibraryDeactivate()) {
            wp_enqueue_script(
                'jquery-ui-touch-punch',
                plugins_url('js/jquery.ui.touch-punch.min.js', WPPROQUIZ_FILE),
                array('jquery-ui-sortable'),
                '0.2.2',
                $footer
            );
        }
    }

    public function shortcode($attr)
    {
        $id = $attr[0];
        $content = '';

        if (!$this->_settings->isJsLoadInHead()) {
            $this->loadJsScripts();
        }

        if (is_numeric($id)) {
            ob_start();

            $this->handleShortCode($id);

            $content = ob_get_contents();

            ob_end_clean();
        }

        if ($this->_settings->isAddRawShortcode()) {
            return '[raw]' . $content . '[/raw]';
        }

        return $content;
    }

    public function handleShortCode($id)
    {
        $view = new WpProQuiz_View_FrontQuiz();

        $quizMapper = new WpProQuiz_Model_QuizMapper();
        $questionMapper = new WpProQuiz_Model_QuestionMapper();
        $categoryMapper = new WpProQuiz_Model_CategoryMapper();
        $formMapper = new WpProQuiz_Model_FormMapper();

        $quiz = $quizMapper->fetch($id);

        $maxQuestion = false;

        if ($quiz->isShowMaxQuestion() && $quiz->getShowMaxQuestionValue() > 0) {

            $value = $quiz->getShowMaxQuestionValue();

            if ($quiz->isShowMaxQuestionPercent()) {
                $count = $questionMapper->count($id);

                $value = ceil($count * $value / 100);
            }

            $question = $questionMapper->fetchAll($id, true, $value);
            $maxQuestion = true;

        } else {
            $question = $questionMapper->fetchAll($id);
        }

        if (empty($quiz) || empty($question)) {
            echo '';

            return;
        }

        $view->quiz = $quiz;
        $view->question = $question;
        $view->category = $categoryMapper->fetchByQuiz($quiz->getId());
        $view->forms = $formMapper->fetch($quiz->getId());

        if ($maxQuestion) {
            $view->showMaxQuestion();
        } else {
            $view->show();
        }
    }

    public function shortcodeToplist($attr)
    {
        $id = $attr[0];
        $content = '';

        if (!$this->_settings->isJsLoadInHead()) {
            $this->loadJsScripts(true, false, true);
        }

        if (is_numeric($id)) {
            ob_start();

            $this->handleShortCodeToplist($id, isset($attr['q']));

            $content = ob_get_contents();

            ob_end_clean();
        }

        if ($this->_settings->isAddRawShortcode() && !isset($attr['q'])) {
            return '[raw]' . $content . '[/raw]';
        }

        return $content;
    }

    private function handleShortCodeToplist($quizId, $inQuiz = false)
    {
        $quizMapper = new WpProQuiz_Model_QuizMapper();
        $view = new WpProQuiz_View_FrontToplist();

        $quiz = $quizMapper->fetch($quizId);

        if ($quiz->getId() <= 0 || !$quiz->isToplistActivated()) {
            echo '';

            return;
        }

        $view->quiz = $quiz;
        $view->points = $quizMapper->sumQuestionPoints($quizId);
        $view->inQuiz = $inQuiz;
        $view->show();
    }

	public function shortcodeStatisticsUser()
	{
		//$id = $attr[0];
		$content = '';

		if (!$this->_settings->isJsLoadInHead()) {
			$this->loadJsScripts();
		}

		/*if (is_numeric($id)) {*/
			ob_start();

			$this->handleShortCodeStatisticsUser();

			$content = ob_get_contents();

			ob_end_clean();
		/*}*/

		if ($this->_settings->isAddRawShortcode()) {
			return '[raw]' . $content . '[/raw]';
		}

		return $content;
	}

	public function handleShortCodeStatisticsUser()
	{


		$quizMapper = new WpProQuiz_Model_QuizMapper();
		$questionMapper = new WpProQuiz_Model_QuestionMapper();
		$categoryMapper = new WpProQuiz_Model_CategoryMapper();
		$formMapper = new WpProQuiz_Model_FormMapper();
		$commentMapper = new WpProQuiz_Model_CommentMapper();
		$statisticRefMapper = new WpProQuiz_Model_StatisticRefMapper();

		//$quiz = $quizMapper->fetch($id);
		$statisticModel = $statisticRefMapper->fetchHistory(1, 0, 100,get_current_user_id(), null,
			null);
		foreach ($statisticModel as $model) {
			/* @var $model WpProQuiz_Model_StatisticHistory */

			if (!$model->getUserId()) {
				$model->setUserName(__('Anonymous', 'wp-pro-quiz'));
			} else {
				if ($model->getUserName() == '') {
					$model->setUserName(__('Deleted user', 'wp-pro-quiz'));
				}
			}

			$sum = $model->getCorrectCount() + $model->getIncorrectCount();
			$result = round(100 * $model->getPoints() / $model->getGPoints(), 2) . '%';

			$model->setResult($result);
			$model->setFormatTime(WpProQuiz_Helper_Until::convertTime($model->getCreateTime(),
				get_option('wpProQuiz_statisticTimeFormat', 'Y/m/d g:i A')));

			$model->setFormatCorrect($model->getCorrectCount() . ' (' . round(100 * $model->getCorrectCount() / $sum,
					2) . '%)');
			$model->setFormatIncorrect($model->getIncorrectCount() . ' (' . round(100 * $model->getIncorrectCount() / $sum,
					2) . '%)');

			$formData = $model->getFormData();
			$formOverview = array();

			foreach ($forms as $form) {
				/* @var $form WpProQuiz_Model_Form */
				if ($form->isShowInStatistic()) {
					$formOverview[] = $formData != null && isset($formData[$form->getFormId()])
						? WpProQuiz_Helper_Form::formToString($form, $formData[$form->getFormId()])
						: '----';
				}
			}
			$model->setFormOverview($formOverview);
		};

		$view = new WpProQuiz_View_FrontStatisticsUser();
		//$view->quiz = $quiz;
		//$view->question = $question;
		//$view->category = $categoryMapper->fetchByQuiz($quiz->getId());
		//$view->forms = $formMapper->fetch($quiz->getId());
		$view->historyModel = $statisticModel;
		$view->comment = $commentMapper->fetchByRefId(get_current_user_id(),1,true);


		/*if ($maxQuestion) {
			$view->showMaxQuestion();
		} else {*/
			$view->show2();
		/*}*/
	}

	public function handleShortCodeStatisticsUserDetails()
	{
		if(isset($_GET['userRefid'])&&isset($_GET['quizId'])) {
			$quizId = 1;//$data['quizId'];
			//$userId = $data['userId'];
			//$refId = $data['refId'];
			$avg = false;
			$refIdUserId =  $_GET['userRefid'];

			$statisticRefMapper = new WpProQuiz_Model_StatisticRefMapper();
			$statisticUserMapper = new WpProQuiz_Model_StatisticUserMapper();
			$formMapper = new WpProQuiz_Model_FormMapper();
			$commentMapper = new WpProQuiz_Model_CommentMapper();

			$statisticUsers = $statisticUserMapper->fetchUserStatistic($refIdUserId, $quizId, $avg);

			$output = array();

			foreach ($statisticUsers as $statistic) {
				/* @var $statistic WpProQuiz_Model_StatisticUser */

				if (!isset($output[$statistic->getCategoryId()])) {
					$output[$statistic->getCategoryId()] = array(
						'questions' => array(),
						'categoryId' => $statistic->getCategoryId(),
						'categoryName' => $statistic->getCategoryId() ? $statistic->getCategoryName() : __('No category',
							'wp-pro-quiz')
					);
				}

				$o = &$output[$statistic->getCategoryId()];

				$o['questions'][] = array(
					'question_id' => $statistic->getQuestionId(),
					'correct' => $statistic->getCorrectCount(),
					'incorrect' => $statistic->getIncorrectCount(),
					'hintCount' => $statistic->getIncorrectCount(),
					'time' => $statistic->getQuestionTime(),
					'points' => $statistic->getPoints(),
					'gPoints' => $statistic->getGPoints(),
					'statistcAnswerData' => $statistic->getStatisticAnswerData(),
					'questionName' => $statistic->getQuestionName(),
					'questionAnswerData' => $statistic->getQuestionAnswerData(),
					'answerType' => $statistic->getAnswerType(),
					'solvedCount' => $statistic->getSolvedCount()
				);
			}

			$view = new WpProQuiz_View_FrontStatisticsUserDetails();

			$view->avg = $avg;
			$view->statisticModel = $statisticRefMapper->fetchByRefId($refIdUserId, $quizId, $avg);
			$view->userName = __('Anonymous', 'wp-pro-quiz');
			$view->CommentModel=$commentMapper->fetchByRefId($refIdUserId,$quizId,false);

			if ($view->statisticModel->getUserId()) {
				$userInfo = get_userdata($view->statisticModel->getUserId());

				if ($userInfo !== false) {
					$view->userName = $userInfo->user_login . ' (' . $userInfo->display_name . ')';
				} else {
					$view->userName = __('Deleted user', 'wp-pro-quiz');
				}
			}

			foreach ($view->CommentModel as $User) {
				$userInfo = get_userdata($User->getUserId());
				$User->setUserName($userInfo->display_name);
			}

			if (!$avg) {
				$view->forms = $formMapper->fetch($quizId);
			}

			$view->userStatistic = $output;

			$view->show();

			/*return json_encode(array(
				'html' => $html
			));*/



		}
	}

	public function handleShortCodeStatisticsUserDetailsBack($id)
	{
		if(isset($_GET['userRefid'])) {
			$refIdUserId=$_GET('userRefid');


			$quizMapper         = new WpProQuiz_Model_QuizMapper();
			$questionMapper     = new WpProQuiz_Model_QuestionMapper();
			$categoryMapper     = new WpProQuiz_Model_CategoryMapper();
			$formMapper         = new WpProQuiz_Model_FormMapper();
			$commentMapper      = new WpProQuiz_Model_CommentMapper();
			$statisticRefMapper = new WpProQuiz_Model_StatisticRefMapper();

			$quiz_user          = $statisticRefMapper->fetchByOnlyRefId( $refIdUserId );

			$statisticModel = $statisticRefMapper->fetchHistory( 1, 0, 0, get_current_user_id(), null,
				null );


			$quiz = $quizMapper->fetch( $id );

			//$maxQuestion = false;

			if ( $quiz->isShowMaxQuestion() && $quiz->getShowMaxQuestionValue() > 0 ) {

				$value = $quiz->getShowMaxQuestionValue();

				if ( $quiz->isShowMaxQuestionPercent() ) {
					$count = $questionMapper->count( $id );

					$value = ceil( $count * $value / 100 );
				}

				$question    = $questionMapper->fetchAll( $id, true, $value );
				$maxQuestion = true;

			} else {
				$question = $questionMapper->fetchAll( $id );
			}

			if ( empty( $quiz ) || empty( $question ) ) {
				echo '';

				return;
			}

			$view = new WpProQuiz_View_FrontStatisticsUserDetails();
			$view->quiz     = $quiz;
			$view->question = $question;
			$view->category = $categoryMapper->fetchByQuiz( $quiz->getId() );
			$view->forms    = $formMapper->fetch( $quiz->getId() );
			$view->comment  = $commentMapper->fetchByRefId( get_current_user_id(), $quiz->getId(), true );

			/*if ($maxQuestion) {
				$view->showMaxQuestion();
			} else {*/
			$view->show1();
			/*}*/
		}
	}

	public function shortcodeStatisticsUserDetails()
	{
		//$id = $attr[0];
		$content = '';

		if (!$this->_settings->isJsLoadInHead()) {
			$this->loadJsScripts();
		}

		/*if (is_numeric($id)) {*/ //comment with $id = $attr[0];
			ob_start();

			$this->handleShortCodeStatisticsUserDetails();

			$content = ob_get_contents();

			ob_end_clean();
		//} //comment with $id = $attr[0];

		if ($this->_settings->isAddRawShortcode()) {
			return '[raw]' . $content . '[/raw]';
		}

		return $content;
	}

    private function loadSettings()
    {
        $mapper = new WpProQuiz_Model_GlobalSettingsMapper();

        $this->_settings = $mapper->fetchAll();
    }

    public static function ajaxQuizLoadData($data)
    {
        $id = $data['quizId'];

        $view = new WpProQuiz_View_FrontQuiz();

        $quizMapper = new WpProQuiz_Model_QuizMapper();
        $questionMapper = new WpProQuiz_Model_QuestionMapper();
        $categoryMapper = new WpProQuiz_Model_CategoryMapper();
        $formMapper = new WpProQuiz_Model_FormMapper();

        $quiz = $quizMapper->fetch($id);

        if ($quiz->isShowMaxQuestion() && $quiz->getShowMaxQuestionValue() > 0) {

            $value = $quiz->getShowMaxQuestionValue();

            if ($quiz->isShowMaxQuestionPercent()) {
                $count = $questionMapper->count($id);

                $value = ceil($count * $value / 100);
            }

            $question = $questionMapper->fetchAll($id, true, $value);

        } else {
            $question = $questionMapper->fetchAll($id);
        }

        if (empty($quiz) || empty($question)) {
            return null;
        }

        $view->quiz = $quiz;
        $view->question = $question;
        $view->category = $categoryMapper->fetchByQuiz($quiz->getId());
        $view->forms = $formMapper->fetch($quiz->getId());

        return json_encode($view->getQuizData());
    }
}