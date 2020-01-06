<?php

class WpProQuiz_Model_LockMapper extends WpProQuiz_Model_Mapper
{
    protected $_table;

    public function __construct()
    {
        parent::__construct();

        $this->_table = $this->_prefix . 'lock';
    }

    /**
     * @param WpProQuiz_Model_Lock $lock
     *
     * @return false|int
     */
    public function insert(WpProQuiz_Model_Lock $lock)
    {
        return $this->_wpdb->insert($this->_table, array(
            'quiz_id' => $lock->getQuizId(),
            'lock_ip' => $lock->getLockIp(),
            'user_id' => $lock->getUserId(),
            'lock_type' => $lock->getLockType(),
            'lock_date' => $lock->getLockDate(),
            'check_user_id' => $lock->getCheckUserId()
        ), array('%d', '%s', '%d', '%d', '%d','%d'));
    }

    /**
     * @param int $quizId
     * @param string $lockIp
     * @param int $userId
     *
     * @return null|WpProQuiz_Model_Lock
     */
    public function fetch($quizId, $lockIp, $userId)
    {
        $row = $this->_wpdb->get_row(
            $this->_wpdb->prepare(
                "SELECT
					*
				FROM
					" . $this->_table . "
				WHERE
					quiz_id = %d 
				AND
					lock_ip = %s
				AND
					user_id = %d",
                $quizId, $lockIp, $userId)
        );

        return $row !== null ? new WpProQuiz_Model_Lock($row) : null;
    }

    /**
     * @param int $quizId
     * @param string $lockIp
     * @param int $userId
     * @param int $type
     *
     * @return bool
     */
    public function isLock($quizId, $lockIp, $userId, $type)
    {
        $c = $this->_wpdb->get_var(
            $this->_wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->_table}
						WHERE quiz_id = %d AND lock_ip = %s AND user_id = %d AND lock_type = %d", $quizId, $lockIp,
                $userId, $type));

        return $c !== null && $c > 0;
    }

    /**
     * @param int $lockTime
     * @param int $quizId
     * @param int $time
     * @param int $type
     * @param bool|false|int $userId
     *
     * @return false|int
     */
    public function deleteOldLock($lockTime, $quizId, $time, $type, $userId = false)
    {
        $user = $userId === false ? '' : ('AND user_id = ' . ((int)$userId));

        return $this->_wpdb->query(
            $this->_wpdb->prepare(
                "DELETE FROM {$this->_table}
					WHERE
						quiz_id = %d AND (lock_date + %d) < %d AND lock_type = %d " . $user,
                $quizId,
                $lockTime,
                $time,
                $type
            )
        );
    }

    public function deleteByQuizId($quizId, $type = false)
    {
        $where = array('quiz_id' => $quizId);
        $whereP = array('%d');

        if ($type !== false) {
            $where = array('quiz_id' => $quizId, 'lock_type' => $type);
            $whereP = array('%d', '%d');
        }

        return $this->_wpdb->delete($this->_tableLock, $where, $whereP);
    }


	/**
	 * @param int $lockTime
	 * @param int $quizId
	 * @param int $time
	 * @param int $type
	 * @param bool|false|int $userId в данном случае statistic_ref_id
	 * * @param bool|false|int $userId
	 *
	 * @return false|int
	 */
	public function deleteOldLockbyCurator($lockTime, $quizId, $time, $type, $userId = false, $checkUserId= false)
	{
		$user = $userId === false ? '' : (' AND user_id = ' . ((int)$userId));
		$ref = $checkUserId === false ? '' : ( ' AND check_user_id = ' . ((int)$checkUserId));

		return $this->_wpdb->query(
			$this->_wpdb->prepare(
				"DELETE FROM {$this->_table}
					WHERE
						quiz_id = %d AND (lock_date + %d) < %d AND lock_type = %d " . $user. $ref,
				$quizId,
				$lockTime,
				$time,
				$type
			)
		);
	}

	/**
	 * @param int $quizId
	 * @param string $lockIp
	 * @param int $userId  в данном случае statistic_ref_id
	 * @param int $type
	 * @param int $CheckUserId
	 *
	 * @return bool
	 */
	public function isLockByCurator($quizId, $lockIp, $userId, $type, $CheckUserId)
	{
		$c = $this->_wpdb->get_var(
			$this->_wpdb->prepare(
				"SELECT COUNT(*) FROM {$this->_table}
						WHERE quiz_id = %d AND lock_ip = %s  AND lock_type = %d AND user_id = %d AND check_user_id <> %d", $quizId, $lockIp,
				 $type, $userId, $CheckUserId));

		return $c !== null && $c > 0;
	}

}