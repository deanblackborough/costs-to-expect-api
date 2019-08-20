<?php
declare(strict_types=1);

namespace App\Validators\Request\Fields;

use App\Validators\Request\Fields\Validator as BaseValidator;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Validation helper class for categories, returns the generated validator objects
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright Dean Blackborough 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class Category extends BaseValidator
{
    /**
     * Create the validation rules for the create request
     *
     * @param integer $resource_type_id
     *
     * @return array
     */
    private function createRules(int $resource_type_id = null): array
    {
        return array_merge(
            [
                'name' => [
                    'required',
                    'string',
                    'unique:category,name,null,id,resource_type_id,' . $resource_type_id
                ],
            ],
            Config::get('api.category.validation.POST.fields')
        );
    }

    /**
     * Return the validator object for the create request
     *
     * @param array $options
     *
     * @return Validator
     */
    public function create(array $options = []): Validator
    {
        $decode = $this->hash->resourceType()->decode(request()->input('resource_type_id'));
        $resource_type_id = null;
        if (count($decode) === 1) {
            $resource_type_id = $decode[0];
        }

        return ValidatorFacade::make(
            array_merge(
                request()->all(),
                [
                    'resource_type_id' => $resource_type_id
                ]
            ),
            $this->createRules(intval($resource_type_id)),
            $this->translateMessages('api.category.validation.POST.messages')
        );
    }

    /**
     * @param array $options
     *
     * @return Validator
     */
    public function update(array $options = []): Validator
    {
        // TODO: Implement update() method.
    }
}