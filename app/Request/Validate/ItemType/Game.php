<?php
declare(strict_types=1);

namespace App\Request\Validate\ItemType;

use App\Entity\Item\Entity;
use App\Request\Validate\Validator as BaseValidator;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2020
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Game extends BaseValidator
{
    public function __construct()
    {
        $this->entity = Entity::byType('game');

        parent::__construct();
    }

    public function create(array $options = []): \Illuminate\Contracts\Validation\Validator
    {
        return $this->createItemValidator();
    }

    public function update(array $options = []): ?\Illuminate\Contracts\Validation\Validator
    {
        $merge_array = [];
        if (array_key_exists('winner_id', request()->all())) {
            $decode = $this->hash->category()->decode(request()->input('winner_id'));
            $winner_id = null;
            if (count($decode) === 1) {
                $winner_id = $decode[0];
            }

            $merge_array = ['winner_id' => $winner_id];
        }

        $messages = [];
        foreach ($this->entity->patchValidationMessages() as $key => $custom_message) {
            $messages[$key] = trans($custom_message);
        }

        return ValidatorFacade::make(
            array_merge(
                request()->all(),
                $merge_array
            ),
            $this->entity->patchValidation(),
            $messages
        );
    }


}
