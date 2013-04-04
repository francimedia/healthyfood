<?php

namespace Manager;


use Fuel;

class Controller_Tracking_Region extends \Controller 
{

	public function action_index()
	{
		$data['tracking_regions'] = \Model_Tracking_Region::find('all');  
		$view = \View::forge('tracking/region/index.twig', $data);
		return \Response::forge($view);  
	}

	public function action_view($id = null)
	{
		is_null($id) and Response::redirect('Tracking_Region');

		if ( ! $data['tracking_region'] = \Model_Tracking_Region::find($id))
		{
			Session::set_flash('error', 'Could not find tracking_region #'.$id);
			Response::redirect('Tracking_Region');
		}

		$this->template->title = "Tracking_region";
		$this->template->content = \View::forge('tracking/region/view', $data);

	}

	public function action_create()
	{
		if (Input::method() == 'POST')
		{
			$val = \Model_Tracking_Region::validate('create');
			
			if ($val->run())
			{
				$tracking_region = \Model_Tracking_Region::forge(array(
				));

				if ($tracking_region and $tracking_region->save())
				{
					Session::set_flash('success', 'Added tracking_region #'.$tracking_region->id.'.');

					Response::redirect('tracking/region');
				}

				else
				{
					Session::set_flash('error', 'Could not save tracking_region.');
				}
			}
			else
			{
				Session::set_flash('error', $val->error());
			}
		}

		$this->template->title = "Tracking_Regions";
		$this->template->content = \View::forge('tracking/region/create');

	}

	public function action_edit($id = null)
	{
		is_null($id) and Response::redirect('Tracking_Region');

		if ( ! $tracking_region = \Model_Tracking_Region::find($id))
		{
			Session::set_flash('error', 'Could not find tracking_region #'.$id);
			Response::redirect('Tracking_Region');
		}

		$val = \Model_Tracking_Region::validate('edit');

		if ($val->run())
		{

			if ($tracking_region->save())
			{
				Session::set_flash('success', 'Updated tracking_region #' . $id);

				Response::redirect('tracking/region');
			}

			else
			{
				Session::set_flash('error', 'Could not update tracking_region #' . $id);
			}
		}

		else
		{
			if (Input::method() == 'POST')
			{

				Session::set_flash('error', $val->error());
			}

			$this->template->set_global('tracking_region', $tracking_region, false);
		}

		$this->template->title = "Tracking_regions";
		$this->template->content = \View::forge('tracking/region/edit');

	}

	public function action_delete($id = null)
	{
		is_null($id) and Response::redirect('Tracking_Region');

		if ($tracking_region = \Model_Tracking_Region::find($id))
		{
			$tracking_region->delete();

			Session::set_flash('success', 'Deleted tracking_region #'.$id);
		}

		else
		{
			Session::set_flash('error', 'Could not delete tracking_region #'.$id);
		}

		Response::redirect('tracking/region');

	}


}