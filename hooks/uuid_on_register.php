//<?php

/* To prevent PHP errors (extending class does not exist) revealing path */
if ( !defined( '\IPS\SUITE_UNIQUE_KEY' ) )
{
	exit;
}

class teamspeak_hook_uuid_on_register extends _HOOK_CLASS_
{
	/**
	 * Build Registration Form
	 *
	 * @return    \IPS\Helpers\Form
	 */
	public static function buildRegistrationForm()
	{
		$form = call_user_func_array( 'parent::buildRegistrationForm', func_get_args() );

		if ( (bool) \IPS\Settings::i()->teamspeak_uuid_on_register )
		{
			$form->add(
				new \IPS\Helpers\Form\Text(
					'teamspeak_uuid', null, (bool) \IPS\Settings::i()->teamspeak_uuid_on_register_force
				), 'password_confirm'
			);
		}

		return $form;
	}

	/**
	 * Create Member
	 *
	 * @param    array $values Values from form
	 * @return    \IPS\Member
	 */
	public static function _createMember( $values )
	{
		$member = call_user_func_array( 'parent::_createMember', func_get_args() );

		if ( isset( $values['teamspeak_uuid'] ) )
		{
			try
			{
				$tsMember = \IPS\teamspeak\Member::i();
				$tsMember->addGroups( $member, $values['teamspeak_uuid'] );

				$uuid = new \IPS\teamspeak\Uuid;
				$uuid->member_id = $member->member_id;
				$uuid->uuid = $values['teamspeak_uuid'];
				$uuid->save();
			}
			catch ( \Exception $e )
			{
				/* Ignore exceptions to not mess up a registration, user can always re-enter their UUID */
			}
		}

		return $member;
	}
}
