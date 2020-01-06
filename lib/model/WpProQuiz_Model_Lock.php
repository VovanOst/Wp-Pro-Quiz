<?php

class WpProQuiz_Model_Lock extends WpProQuiz_Model_Model
{
    protected $_quizId;
    protected $_lockIp;
    protected $_lockDate;
    protected $_userId;//может быть user_id или statistic_ref_id
    protected $_lockType;
	protected $_checkUserId;// userId проверяющего тесты

    const TYPE_STATISTIC = 1;
    const TYPE_QUIZ = 2;
	const TYPE_ADMIN_LOCK = 3;

    public function setQuizId($_quizId)
    {
        $this->_quizId = $_quizId;

        return $this;
    }

    public function getQuizId()
    {
        return $this->_quizId;
    }

    public function setLockIp($_lockIp)
    {
        $this->_lockIp = $_lockIp;

        return $this;
    }

    public function getLockIp()
    {
        return $this->_lockIp;
    }

    public function setLockDate($_lockDate)
    {
        $this->_lockDate = $_lockDate;

        return $this;
    }

    public function getLockDate()
    {
        return $this->_lockDate;
    }

    public function setUserId($_userId)
    {
        $this->_userId = (int)$_userId;

        return $this;
    }

    public function getUserId()
    {
        return $this->_userId;
    }

    public function setLockType($_lockType)
    {
        $this->_lockType = (int)$_lockType;

        return $this;
    }

    public function getLockType()
    {
        return $this->_lockType;
    }

	public function setCheckUserId($_userId)
	{
		$this->_checkUserId = (int)$_userId;

		return $this;
	}

	public function getCheckUserId()
	{
		return $this->_checkUserId;
	}
}