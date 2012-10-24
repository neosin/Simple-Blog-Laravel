<?php

class Blog_Controller extends Base_Controller {

	public $restful = true;

	public function get_index()
	{
		// code here..

		return View::make('blog.index');
	}

	public function get_home()
	{
		// code here..

		return View::make('blog.home');
	}

	public function get_profile()
	{
		// code here..

		return View::make('blog.profile');
	}

	public function post_article()
	{
		// code here..

		return View::make('blog.article');
	}

}
