<?php
declare(strict_types=1);

namespace App\Item;

use App\Models\ItemTypeAllocatedExpense;
use Illuminate\Database\Eloquent\Model;

/**
 * The Interface for dealing with allocated expenses, everything should be
 * funneled through an instance of this class
 *
 * @author Dean Blackborough <dean@g3d-development.com>
 * @copyright G3D Development Limited 2018-2019
 * @license https://github.com/costs-to-expect/api/blob/master/LICENSE
 */
class AllocatedExpense extends AbstractItem
{
    /**
     * Return the parameters config string specific to the item type
     *
     * @return string
     */
    public function collectionParametersConfig(): string
    {
        return 'api.item-type-allocated-expense.parameters.collection';
    }

    /**
     * Return the model instance for the item type
     *
     * @return Model
     */
    public function model(): Model
    {
        return new ItemTypeAllocatedExpense();
    }

    /**
     * Return the patch fields specific to the item type, these will be merged
     * with any default patch fields
     *
     * @return array
     */
    public function patchFields(): array
    {
        // TODO: Implement patchFields() method.
    }

    /**
     * Return the post fields config string specific to the item type
     *
     * @return string
     */
    public function postFieldsConfig(): string
    {
        return 'api.item-type-allocated-expense.fields';
    }

    /**
     * Return the search parameters config string specific to the item type
     *
     * @return string
     */
    public function searchParametersConfig(): string
    {
        return 'api.item-type-allocated-expense.searchable';
    }

    /**
     * Return the show parameters config string specific to the item type
     *
     * @return string
     */
    public function showParametersConfig(): string
    {
        return 'api.item-type-allocated-expense.parameters.item';
    }

    /**
     * Return the sort parameters config string specific to the item type
     *
     * @return string
     */
    public function sortParametersConfig(): string
    {
        return 'api.item-type-allocated-expense.sortable';
    }
}
