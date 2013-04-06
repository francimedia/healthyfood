<?php

class Model_Keyword extends \Orm\Model
{

    protected static $_table_name = 'keyword';

	protected static $_properties = array(
		'id',
		'value',
		'count',
	);

}
