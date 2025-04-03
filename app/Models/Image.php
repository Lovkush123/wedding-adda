namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone']; // Adjust as needed

    // Define relationship to Image model
    public function images(): HasMany
    {
        return $this->hasMany(Image::class, 'vendor_id');
    }
}
