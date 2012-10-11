<?php

class Post extends Eloquent {

	public static $timestamps = true;

	public function user()
	{
		return $this->belongs_to('User','post_author');
	}

}
