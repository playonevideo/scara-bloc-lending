<?php

namespace Database\Seeders;

use App\Enums\LoanStatus;
use App\Enums\ObjectStatus;
use App\Models\Apartment;
use App\Models\Building;
use App\Models\Category;
use App\Models\Conversation;
use App\Models\Floor;
use App\Models\Item;
use App\Models\Loan;
use App\Models\Message;
use App\Models\Review;
use App\Models\Staircase;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
        ]);

        $building = Building::factory()->create(['name' => 'Bloc A', 'address' => 'Str. Exemplu, Nr. 10']);
        $staircase = Staircase::factory()->for($building)->create(['name' => 'Scara 1']);

        $apartments = collect();
        foreach (range(0, 10) as $floorNumber) {
            $floor = Floor::factory()->for($staircase)->create(['number' => $floorNumber]);
            foreach (range(1, 4) as $apartmentNumber) {
                $apartments->push(
                    Apartment::factory()->for($floor)->create(['number' => $floorNumber * 4 + $apartmentNumber - 3])
                );
            }
        }

        $superAdmin = User::factory()->superAdmin()->create([
            'name' => 'Administrator General',
            'email' => 'admin@vecini.ro',
            'phone' => '+40700000001',
        ]);

        User::factory()->admin()->create([
            'name' => 'Administrator Scară',
            'email' => 'admin2@vecini.ro',
            'phone' => '+40700000002',
        ]);

        $residents = collect();
        $firstNames = ['Andrei', 'Mihai', 'Ioana', 'Elena', 'Cristian', 'Maria', 'Alexandru', 'Ana', 'Vlad', 'Raluca', 'George', 'Simona', 'Radu', 'Daniela', 'Tudor'];
        foreach ($firstNames as $index => $firstName) {
            $residents->push(User::factory()->create([
                'name' => $firstName.' '.Str::upper(Str::random(1)).'.',
                'email' => strtolower($firstName).'@vecini.ro',
                'apartment_id' => $apartments[$index % $apartments->count()]->id,
                'phone' => '+4072'.random_int(1000000, 9999999),
            ]));
        }

        $titles = [
            'Bormașină Bosch', 'Scară telescopică', 'Aspirator profesional', 'Mașină de găurit',
            'Set de șurubelnițe', 'Cărucior pentru copii', 'Masă pliabilă', 'Set de scaune',
            'Fierăstrău electric', 'Aparat de sudură', 'Grătar portabil', 'Trambulină fitness',
            'Colecție de cărți SF', 'Proiector video', 'Boxă portabilă', 'Aerotermă',
            'Pompa de bicicletă', 'Set de chei', 'Flex', 'Nivelă cu laser', 'Cort de camping',
            'Sanie', 'Bicicletă de oraș', 'Mixer de bucătărie', 'Mașină de cusut',
        ];

        $categories = Category::all();
        $objects = collect();

        foreach ($titles as $index => $title) {
            $owner = $residents[$index % $residents->count()];

            $objects->push(Item::factory()->create([
                'owner_id' => $owner->id,
                'category_id' => $categories[$index % $categories->count()]->id,
                'title' => $title,
                'description' => 'Obiect disponibil pentru împrumut în comunitate. Se predă personal, în stare bună de funcționare.',
                'max_borrow_days' => random_int(2, 14),
                'status' => ObjectStatus::Available,
            ]));
        }

        $this->seedLoans($objects, $residents);
        $this->seedConversations($residents);
        $this->seedReviews($objects, $residents);
    }

    private function seedLoans($objects, $residents): void
    {
        foreach ($objects->take(10) as $index => $object) {
            $owner = $object->owner;
            $borrower = $residents->first(fn (User $r) => $r->id !== $owner->id);

            $status = [LoanStatus::Requested, LoanStatus::Accepted, LoanStatus::Borrowed, LoanStatus::Completed][$index % 4];

            $loan = Loan::factory()->create([
                'object_id' => $object->id,
                'borrower_id' => $borrower->id,
                'lender_id' => $owner->id,
                'status' => $status,
            ]);

            if ($status === LoanStatus::Accepted) {
                $object->update(['status' => ObjectStatus::Reserved]);
            } elseif ($status === LoanStatus::Borrowed) {
                $object->update(['status' => ObjectStatus::Borrowed]);
            }
        }
    }

    private function seedConversations($residents): void
    {
        foreach ($residents->take(5) as $index => $resident) {
            $other = $residents[$index + 5] ?? $residents[0];

            $conversation = Conversation::create();
            $conversation->participants()->attach([$resident->id, $other->id]);

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $resident->id,
                'body' => 'Bună! Este disponibilă bormașina în weekend?',
                'read_at' => now(),
            ]);

            Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $other->id,
                'body' => 'Salut! Da, o poți lua sâmbătă.',
            ]);
        }
    }

    private function seedReviews($objects, $residents): void
    {
        foreach ($objects->take(6) as $index => $object) {
            $reviewer = $residents->first(fn (User $r) => $r->id !== $object->owner_id);

            Review::create([
                'loan_id' => Loan::factory()->create([
                    'object_id' => $object->id,
                    'borrower_id' => $reviewer->id,
                    'lender_id' => $object->owner_id,
                    'status' => LoanStatus::Completed,
                ])->id,
                'reviewer_id' => $reviewer->id,
                'reviewee_id' => $object->owner_id,
                'rating' => random_int(4, 5),
                'comment' => 'Vecin de încredere, recomand!',
            ]);
        }
    }
}
