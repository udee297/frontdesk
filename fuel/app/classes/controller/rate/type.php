<?php

class Controller_Rate_Type extends Controller_Authenticate
{
	public function action_index()
	{
		$data['rate_type'] = Model_Rate_Type::find('all');
		$this->template->title = "Rate Type";
		$this->template->content = View::forge('rate/type/index', $data);

	}

	public function action_view($id = null)
	{
		is_null($id) and Response::redirect('rate/type');

		if ( ! $data['rate_type'] = Model_Rate_Type::find($id))
		{
			Session::set_flash('error', 'Could not find rate #'.$id);
			Response::redirect('facilities/rate-type');
		}

		$this->template->title = "Rate Type";
		$this->template->content = View::forge('rate/type/view', $data);

	}

	public function action_create()
	{
		if (Input::method() == 'POST')
		{
			$val = Model_Rate_Type::validate('create');

			if ($val->run())
			{
				$rate_type = Model_Rate_Type::forge(array(
					'name' => Input::post('name'),
					'description' => Input::post('description'),
					'enabled' => Input::post('enabled'),
					'fdesk_user' => Input::post('fdesk_user'),
				));

				if ($rate_type and $rate_type->save())
				{
					Session::set_flash('success', 'Added rate type #'.$rate_type->name.'.');

					Response::redirect('facilities/rate-type');
				}

				else
				{
					Session::set_flash('error', 'Could not save rate type.');
				}
			}
			else
			{
				Session::set_flash('error', $val->error());
			}
		}

		$this->template->title = "Rate Type";
		$this->template->content = View::forge('rate/type/create');

	}

	public function action_edit($id = null)
	{
		is_null($id) and Response::redirect('facilities/rate-type');

		if ( ! $rate_type = Model_Rate_Type::find($id))
		{
			Session::set_flash('error', 'Could not find rate #'.$id);
			Response::redirect('facilities/rate-type');
		}

		$val = Model_Rate_Type::validate('edit');

		if ($val->run())
		{
			$rate_type->name = Input::post('name');
			$rate_type->description = Input::post('description');
            $rate_type->enabled = Input::post('enabled');
            $rate_type->fdesk_user = Input::post('fdesk_user');

			if ($rate_type->save())
			{
				Session::set_flash('success', 'Updated rate type #' . $rate_type->name);

				Response::redirect('facilities/rate-type');
			}

			else
			{
				Session::set_flash('error', 'Could not update rate type #' . $id);
			}
		}

		else
		{
			if (Input::method() == 'POST')
			{
				$rate_type->name = $val->validated('name');
				$rate_type->description = $val->validated('description');
                $rate_type->enabled = $val->validated('enabled');
                $rate_type->fdesk_user = $val->validated('fdesk_user');

				Session::set_flash('error', $val->error());
			}

			$this->template->set_global('rate_type', $rate_type, false);
		}

		$this->template->title = "Rate Type";
		$this->template->content = View::forge('rate/type/edit');

	}

	public function action_delete($id = null)
	{
		is_null($id) and Response::redirect('rate/type');

		if (Input::method() == 'POST')
		{		
			if ($rate_type = Model_Rate_Type::find($id))
			{
				$rate = Model_Rate::find('first', array('where' => array('rate_id' => $id)));
				if ($rate)
					Session::set_flash('error', 'Rate type is already in use by Rate(s).');
				else
				{
					$rate_type->delete();
					Session::set_flash('success', 'Deleted rate type #'.$id);
				}
			}
			else
			{
				Session::set_flash('error', 'Could not delete rate type #'.$id);
			}
		}
		else
		{
			Session::set_flash('error', 'Delete is not allowed');
		}
		
		Response::redirect('facilities/rate-type');

	}


}
