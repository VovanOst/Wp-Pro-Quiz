<?php

class WpProQuiz_Model_Comment extends WpProQuiz_Model_Model
{
   /* const FORM_TYPE_TEXT = 0;
    const FORM_TYPE_TEXTAREA = 1;
    const FORM_TYPE_NUMBER = 2;
    const FORM_TYPE_CHECKBOX = 3;
    const FORM_TYPE_EMAIL = 4;
    const FORM_TYPE_YES_NO = 5;
    const FORM_TYPE_DATE = 6;
    const FORM_TYPE_SELECT = 7;
    const FORM_TYPE_RADIO = 8;*/

    protected $_commentId = 0;
    protected $_quizId = 0;
	protected $_questionId = 0;
    protected $_statisticRefId=0;
	protected $_comment='';
	protected $_createTime = 0;
	protected $_userId = 0;
	protected $_userName = '';
   // protected $_fieldname = '';
   // protected $_type = 0;
  //  protected $_required = false;
  //  protected $_sort = 0;
   // protected $_data = null;
   // protected $_showInStatistic = false;

    public function setCommentId($_commentId)
    {
        $this->_commentId = (int)$_commentId;

        return $this;
    }

    public function getCommentId()
    {
        return $this->_commentId;
    }

    public function setQuizId($_quizId)
    {
        $this->_quizId = (int)$_quizId;

        return $this;
    }

    public function getQuizId()
    {
        return $this->_quizId;
    }

	public function setQuestionId($_questionId)
	{
		$this->_questionId = (int)$_questionId;

		return $this;
	}

	public function getQuestionId()
	{
		return $this->_questionId;
	}

	public function setStatisticRefId($_statisticRefId)
	{
		$this->_statisticRefId = (int)$_statisticRefId;

		return $this;
	}

	public function getStatisticRefId()
	{
		return $this->_statisticRefId;
	}


	public function setComment($_comment)
	{
		$this->_comment = (string)$_comment;

		return $this;
	}

	public function getComment()
	{
		return $this->_comment;
	}


	public function setCreateTime($_createTime)
	{
		$this->_createTime = (int)$_createTime;

		return $this;
	}

	public function getCreateTime()
	{
		return $this->_createTime;
	}

	public function getUserId()
	{
		return $this->_userId;
	}

	public function setUserId($_userId)
	{
		$this->_userId = (int)$_userId;

		return $this;
	}

	public function setUserName($_userName)
	{
		$this->_userName = (string)$_userName;

		return $this;
	}



	public function getUserName()
	{
		return $this->_userName;
	}

	public function setMinCreateTime($_minCreateTime)
	{
		$this->_minCreateTime = (int)$_minCreateTime;

		return $this;
	}

	public function getMinCreateTime()
	{
		return $this->_minCreateTime;
	}

	public function setMaxCreateTime($_maxCreateTime)
	{
		$this->_maxCreateTime = (int)$_maxCreateTime;

		return $this;
	}

	public function getMaxCreateTime()
	{
		return $this->_maxCreateTime;
	}
    /*public function setFieldname($_fieldname)
    {
        $this->_fieldname = (string)$_fieldname;

        return $this;
    }*/

  /*  public function getFieldname()
    {
        return $this->_fieldname;
    }

    public function setType($_type)
    {
        $this->_type = (int)$_type;

        return $this;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function setRequired($_required)
    {
        $this->_required = (bool)$_required;

        return $this;
    }

    public function isRequired()
    {
        return $this->_required;
    }

    public function setSort($_sort)
    {
        $this->_sort = (int)$_sort;

        return $this;
    }

    public function getSort()
    {
        return $this->_sort;
    }*/
	/*
	public function setData($_data)
	{
		$this->_data = $_data === null ? null : (array)$_data;

		return $this;
	}

	public function getData()
	{
		return $this->_data;
	}

	public function setShowInStatistic($_showInStatistic)
	{
		$this->_showInStatistic = (bool)$_showInStatistic;

		return $this;
	}

	public function isShowInStatistic()
	{
		return $this->_showInStatistic;
	}*/
}