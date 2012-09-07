<?php

/**
 * Description of code_model
 *
 * @author robertopereznygaard
 */
class CodeModel extends CodazoModel
{

	public function __construct()
	{
		parent::__construct();
		$this->_tableName = 'code';
	}

	public function saveCode($values)
	{
		$this->begin();

		$insertedId = $this->save($values);

		$ruid = new ReversibleUniqueId();
		$uniqueId = $ruid->encode($insertedId);

		$this->update(array('unique_id' => $uniqueId), array('id' => $insertedId));

		$this->commit();

		return $uniqueId;
	}

}
