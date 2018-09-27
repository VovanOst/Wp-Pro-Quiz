<?php

class WpProQuiz_Model_CommentMapper extends WpProQuiz_Model_Mapper
{

   /* public function deleteComment($commentIds, $quizId)
    {
        return $this->_wpdb->query(
            $this->_wpdb->prepare('
				DELETE FROM ' . $this->_tableForm . '
				WHERE
					comment_id IN(' . implode(', ', array_map('intval', (array)$commentIds)) . ') AND quiz_id = %d
			', $quizId)
        );
    }*/

    /**
     * @param WpProQuiz_Model_Comment[] $comments
     */
   /* public function update($comments)
    {
        $values = $values2 = array();

        foreach ($comments as $comment) {*/
            /* @var $form WpProQuiz_Model_Form */

          /*  $data = array(
                $comment->getFormId(),
                $comment->getQuizId(),
                $comment->getFieldname(),
                $comment->getType(),
                (int)$comment->isRequired(),
                $comment->getSort(),
                (int)$comment->isShowInStatistic()
            );

            if ($form->getData() === null) {
                $values[] = '(' . $this->_wpdb->prepare('%d, %d, %s, %d, %d, %d, %d', $data) . ')';
            } else {
                $data[] = @json_encode($form->getData());
                $values2[] = '(' . $this->_wpdb->prepare('%d, %d, %s, %d, %d, %d, %d, %s', $data) . ')';
            }
        }

        if (!empty($values)) {
            $this->_wpdb->query('
				REPLACE INTO ' . $this->_tableForm . '
					(form_id, quiz_id, fieldname, type, required, sort, show_in_statistic)
				VALUES ' . implode(', ', $values) . '
			');
        }

        if (!empty($values2)) {
            $this->_wpdb->query('
				REPLACE INTO ' . $this->_tableForm . '
					(form_id, quiz_id, fieldname, type, required, sort, show_in_statistic, data)
				VALUES ' . implode(', ', $values2) . '
			');
        }
    }*/

    /**
     * @param $quizId
     * @return WpProQuiz_Model_Form[]
     */
   /* public function fetch($quizId)
    {
        $results = $this->_wpdb->get_results(
            $this->_wpdb->prepare('
						SELECT * FROM ' . $this->_tableForm . ' WHERE quiz_id = %d ORDER BY sort', $quizId), ARRAY_A);
        $a = array();

        foreach ($results as $row) {
            $row['data'] = $row['data'] === null ? null : @json_decode($row['data'], true);

            $a[] = new WpProQuiz_Model_Form($row);
        }

        return $a;
    }*/
	public function fetchAllByRef($statisticRefId)
	{
		$a = array();

		$results = $this->_wpdb->get_results(
			$this->_wpdb->prepare(
				'SELECT
							*
						FROM
							' . $this->_tableComment . '
						WHERE
							statistic_ref_id = %d', $statisticRefId),
			ARRAY_A);

		foreach ($results as $row) {
			$a[] = new WpProQuiz_Model_Comment($row);
		}

		return $a;
	}

	public function fetchByRefId($refIdUserId, $quizId, $avg = false)
	{
		$where = $avg ? 'comment.user_id = %d' : 'comment.statistic_ref_id = %d';
		$results = $this->_wpdb->get_results(
			$this->_wpdb->prepare(
				"SELECT
					comment.*
				FROM 
					{$this->_tableComment} AS comment 
				WHERE 
					{$where} AND comment.quiz_id = %d"
				, $refIdUserId, $quizId)
			, ARRAY_A);
		$r = array();
		foreach ($results as $row) {
			$r[] = new WpProQuiz_Model_Comment($row);
		}

		return $r;
	}

}