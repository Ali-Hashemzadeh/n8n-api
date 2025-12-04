<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use App\Models\Company;
use App\Models\Customer;
use App\Models\ServiceType;
use App\Models\CallReport;
use App\Models\User;
use Carbon\Carbon;

class RealDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $this->command->info('1. Creating Global Customer Pool (20 People)...');
        // We create a pool of customers first so they can be shared across companies
        $customers = [];
        for ($i = 0; $i < 20; $i++) {
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;

            $customer = Customer::firstOrCreate(
                ['email' => strtolower("$firstName.$lastName@example.com")],
                [
                    'phone' => $faker->phoneNumber,
                    'name' => $firstName,
                    'lastname' => $lastName,
                ]
            );
            $customers[] = $customer;
        }

        $companiesData = [
            ['name' => 'Apex Dental Clinic', 'types' => ['Root Canal', 'Cleaning', 'Checkup', 'Whitening']],
            ['name' => 'Legal Eagles LLP', 'types' => ['Consultation', 'Contract Review', 'Litigation']],
            ['name' => 'TechFix Solutions', 'types' => ['Hardware Repair', 'Software Install', 'Diagnostics']],
        ];

        foreach ($companiesData as $compData) {
            $this->command->info("2. Setting up Company: {$compData['name']}...");

            // 1. Create Company
            $company = Company::firstOrCreate(['name' => $compData['name']]);

            // 2. Create Service Types
            $serviceTypeIds = [];
            foreach ($compData['types'] as $typeName) {
                $st = ServiceType::firstOrCreate([
                    'company_id' => $company->id,
                    'name' => $typeName
                ]);
                $serviceTypeIds[] = $st->id;
            }

            // 3. Assign 5-10 Random Customers to this Company (Many-to-Many)
            // We use the global pool we created earlier
            $companyCustomers = collect($customers)->random(rand(5, 12));

            // Sync without detaching to ensure we don't break other company links if running multiple times
            $company->customers()->syncWithoutDetaching($companyCustomers->pluck('id'));

            // 4. Create Call Reports for this Company
            $this->command->info("   - Generating 10 Call Reports...");

            foreach ($companyCustomers->take(10) as $customer) {
                // Generate a realistic conversation
                $scenario = $this->generateRealisticConversation($faker, $customer->name);

                $report = CallReport::create([
                    'company_id' => $company->id,
                    'customer_id' => $customer->id,
                    'summary' => $scenario['summary'],
                    'conversation' => $scenario['conversation'], // Array, automatically cast to JSON by model
                    'metadata' => [
                        'duration_seconds' => $faker->numberBetween(45, 600),
                        'sentiment' => $faker->randomElement(['positive', 'neutral', 'negative']),
                        'tags' => $faker->words(3),
                        'cost_estimate' => $faker->numberBetween(50, 500)
                    ],
                    'state' => $faker->randomElement(['confirmed', 'failed', 'unfinished']),
                    'created_at' => $faker->dateTimeBetween('-1 month', 'now'),
                ]);

                // Attach 1 or 2 random Service Types to this report
                $randomServiceTypes = collect($serviceTypeIds)->random(rand(1, 2));
                $report->serviceTypes()->attach($randomServiceTypes);
            }
        }

        $this->command->info('Seeding Complete! Created 3 Companies, Service Types, and linked Customers/Reports.');
    }

    /**
     * Helper to generate realistic conversation JSON
     */
    private function generateRealisticConversation($faker, $customerName)
    {
        $scenarios = [
            'booking' => [
                'summary' => "Customer wanted to schedule an appointment.",
                'lines' => [
                    ['AI', "Hello, thanks for calling. How can I help you today?"],
                    ['User', "Hi, I'd like to book an appointment for next Tuesday."],
                    ['AI', "Sure, I have openings at 10 AM and 2 PM. Which works for you?"],
                    ['User', "10 AM sounds good."],
                    ['AI', "Great, I have you booked for Tuesday at 10 AM. Anything else?"],
                    ['User', "No, that's it. Thanks."],
                    ['AI', "You're welcome, $customerName. Have a great day!"]
                ]
            ],
            'complaint' => [
                'summary' => "Customer reported an issue with previous service.",
                'lines' => [
                    ['AI', "Hello, how can I assist you?"],
                    ['User', "I'm calling because I'm not happy with the service I received yesterday."],
                    ['AI', "I'm very sorry to hear that. Could you provide more details?"],
                    ['User', "The technician arrived late and didn't fix the issue completely."],
                    ['AI', "I understand. Let me escalate this to a manager immediately."],
                    ['User', "Okay, please do."],
                ]
            ],
            'inquiry' => [
                'summary' => "Inquiry about pricing and availability.",
                'lines' => [
                    ['AI', "Thanks for calling. What information do you need?"],
                    ['User', "Hi, I was wondering how much you charge for a standard consultation?"],
                    ['AI', "Our standard consultation fee is $50."],
                    ['User', "Okay, and are you open on weekends?"],
                    ['AI', "Yes, we are open Saturdays from 9 to 5."],
                    ['User', "Perfect, thanks for the info."]
                ]
            ]
        ];

        $key = $faker->randomElement(array_keys($scenarios));
        $data = $scenarios[$key];

        // Format into the JSON structure your frontend expects
        $formattedTranscript = [];
        foreach ($data['lines'] as $line) {
            $formattedTranscript[] = [
                'speaker' => $line[0],
                'text' => $line[1]
            ];
        }

        return [
            'summary' => $data['summary'],
            'conversation' => ['transcript' => $formattedTranscript] // Wrapped in 'transcript' key based on your DB dump
        ];
    }
}
