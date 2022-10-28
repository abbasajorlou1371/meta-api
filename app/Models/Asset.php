<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;

    /**
 * App\Models\Asset
 *
 * @property int $id
 * @property int $variable_id
 * @property string $count
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Variable $variable
 * @method static \Illuminate\Database\Eloquent\Builder|Asset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Asset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Asset query()
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Asset whereVariableId($value)
 * @mixin \Eloquent
 */
class Asset extends Model
    {
        use HasFactory;

        protected $table = "assets";

        protected $fillable = [
            'user_id',
            'psc', 'irr', 'blue', 'red', 'green', 'satisfaction', 'effect',
        ];


    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
        {
            return $this->belongsTo(User::class);
        }

    /**
     * @return BelongsTo
     */
    public function variable(): BelongsTo
        {
            return $this->belongsTo(Variable::class);
        }


    }
