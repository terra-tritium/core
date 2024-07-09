<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('builds')->insert([
            "name" => "Colonization",
            "code" => 1,
            "image" => "build-01.png",
            "description" => "The colonization module is a floating dream, a flying house that takes humanity beyond all limits. It is a ship of hope, carrying a valuable cargo of human life and resources to the universe's next frontier. It is a blend of engineering and artistry, designed to survive in hostile environments and provide comfort and safety for those who inhabit it",
            "effect" => "Each level of expansion allows the colonization of a new planet",
            "metalStart" => 100,
            "uraniumStart" => 50,
            "crystalStart" => 50,
            "metalLevel" => 1,
            "uraniumLevel" => 2,
            "crystalLevel" => 3,
            "coefficient" => 150
        ]);

        DB::table('builds')->insert([
            "name" => "Energy Collector",
            "code" => 2,
            "image" => "build-02.png",
            "description" => "The Energy Harvesting is a dance of light and movement, where nature is converted into a source in a clean and inexhaustible way. It is the realization of the human ambition to find clean and renewable energy sources that can sustain the new world in constant evolution. With its bold shapes and sophisticated technology, the Energy Harvesting is like a graceful dancer, dancing with the sun and wind to fuel our future.",
            "effect" => "Allows capturing and storing energy through solar panels Enables building battery houses",
            "metalStart" => 100,
            "uraniumStart" => 50,
            "crystalStart" => 50,
            "metalLevel" => 1,
            "uraniumLevel" => 10,
            "crystalLevel" => 20,
            "coefficient" => 50
        ]);

        DB::table('builds')->insert([
            "name" => "Humanoid Factory",
            "code" => 3,
            "image" => "build-03.png",
            "description" => "The humanoid robot factory is a symphony of technology and creativity, where artificial intelligence and engineering come together to create amazing machines that mimic the human form. It is a mechanical work of art, where science and art complement each other to produce humanoid robots capable of performing complex tasks and interacting with the world around us. It is the vision of a future where technology and humanity work together to improve life and society.",
            "effect" => "Allows construction of new humanoid robots, each level increases the capacity and maximum quantity.",
            "metalStart" => 100,
            "uraniumStart" => 50,
            "crystalStart" => 50,
            "metalLevel" => 1,
            "uraniumLevel" => 10,
            "crystalLevel" => 20,
            "coefficient" => 200
        ]);

        DB::table('builds')->insert([
            "name" => "Metal Mining",
            "code" => 4,
            "image" => "build-04.png",
            "description" => "Metal miner is an ode to efficiency and preservation, where advanced technology meets concern for the environment to responsibly extract the treasures of the planets. It's a symphony of robots and machinery, working in perfect harmony with the hostile environment of any wild planet. to ensure the sustainable production of precious metals. It's a vision of a future where humanity can meet its needs without harming the planet, but instead protecting it for future generations.",
            "effect" => "Enables Metal mining. Essential for the construction of new buildings and machines",
            "metalStart" => 100,
            "uraniumStart" => 50,
            "crystalStart" => 50,
            "metalLevel" => 1,
            "uraniumLevel" => 10,
            "crystalLevel" => 20,
            "coefficient" => 50
        ]);

        DB::table('builds')->insert([
            "name" => "Uranium Mining",
            "code" => 5,
            "image" => "build-05.png",
            "description" => "The Uranium Extraction Mine is a masterpiece of technology and engineering, where advanced knowledge on how to extract this strong and malleable element, transforming it into raw material for new constructions and advanced machines.",
            "effect" => "With each level, the mining capacity of the Uranium mineral increases",
            "metalStart" => 100,
            "uraniumStart" => 50,
            "crystalStart" => 50,
            "metalLevel" => 1,
            "uraniumLevel" => 10,
            "crystalLevel" => 20,
            "coefficient" => 100
        ]);

        DB::table('builds')->insert([
            "name" => "Crystal Mining",
            "code" => 6,
            "image" => "build-06.png",
            "description" => "The crystal extraction mine is a masterpiece of technology and engineering, where advanced knowledge of science is applied to extract the rare and powerful element that is crystal, transforming it into a powerful source of energy.",
            "effect" => "With each level, the mining capacity of the Crystal mineral increases",
            "metalStart" => 100,
            "uraniumStart" => 50,
            "crystalStart" => 50,
            "metalLevel" => 1,
            "uraniumLevel" => 10,
            "crystalLevel" => 20,
            "coefficient" => 150
        ]);

        DB::table('builds')->insert([
            "name" => "Laboratory",
            "code" => 7,
            "image" => "build-08.png",
            "description" => "The Laboratory is a temple of innovation and creativity, where imagination becomes reality. It's a place where science and technology merge to produce the wonders of the future, from amazing advances in robotics and artificial intelligence to revolutionary inventions in energy and transportation. It is a vision of a bright future where humanity continues to explore and grow, supported by amazing and advanced technologies.",
            "effect" => "Enables the Scientist guardian.At each level, it allows the discovery and development of new technologies",
            "metalStart" => 100,
            "uraniumStart" => 50,
            "crystalStart" => 50,
            "metalLevel" => 1,
            "uraniumLevel" => 10,
            "crystalLevel" => 20,
            "coefficient" => 50
        ]);

        DB::table('builds')->insert([
            "name" => "Warehouse",
            "code" => 8,
            "image" => "build-09.png",
            "description" => "The Mineral Resources Warehouse is a masterpiece of efficiency and technology, where advanced robotics and artificial intelligence work in perfect harmony to store and manage products quickly and accurately. It's a labyrinth of automated aisles and efficient robots, moving with surgical precision to meet the storage needs of the modern world. It's a vision of a future where logistics are simplified and technology is used to make life easier and more efficient.",
            "effect" => "With each level, resource storage capacity increases",
            "metalStart" => 100,
            "uraniumStart" => 50,
            "crystalStart" => 50,
            "metalLevel" => 1,
            "uraniumLevel" => 10,
            "crystalLevel" => 20,
            "coefficient" => 100
        ]);

        DB::table('builds')->insert([
            "name" => "Shipyard",
            "code" => 9,
            "image" => "build-10.png",
            "description" => "Shipyard construction factory",
            "effect" => "",
            "metalStart" => 100,
            "uraniumStart" => 50,
            "crystalStart" => 50,
            "metalLevel" => 1,
            "uraniumLevel" => 10,
            "crystalLevel" => 20,
            "coefficient" => 100
        ]);

        DB::table('builds')->insert([
            "name" => "Batery House",
            "code" => 10,
            "image" => "build-11.png",
            "description" => "The Battery House is a monument to innovation and sustainability, where advanced technology meets concern for the environment to ensure a clean and renewable source of energy. It's a sanctuary of energy efficiency, where solar panels, advanced batteries and other technologies work together to efficiently and reliably store and distribute energy. It's a vision of a future where humanity can meet its energy needs without harming the planet, but instead protecting it for future generations.",
            "effect" => "With each level, energy storage efficiency increases",
            "metalStart" => 100,
            "uraniumStart" => 50,
            "crystalStart" => 50,
            "metalLevel" => 1,
            "uraniumLevel" => 10,
            "crystalLevel" => 20,
            "coefficient" => 100
        ]);

        DB::table('builds')->insert([
            "name" => "Military Camp",
            "code" => 11,
            "image" => "build-12.png",
            "description" => "The military training ground is a celestial academy of excellence, where brilliant minds and strong bodies come together to form space warriors. It is a sophisticated and advanced environment, equipped with advanced simulation and training technologies, designed to prepare space warriors. for the most challenging missions in space. It is a vision of a future where humanity is prepared to explore, protect and preserve the universe. It is a testament to human dedication to security and the defense of life and liberty, bringing hope and peace to future generations.",
            "effect" => "At each level, it allows the development of new weapons and machines, in addition to increasing the ability to create new military robots.",
            "metalStart" => 100,
            "uraniumStart" => 50,
            "crystalStart" => 50,
            "metalLevel" => 1,
            "uraniumLevel" => 10,
            "crystalLevel" => 20,
            "coefficient" => 100
        ]);

        DB::table('builds')->insert([
            "name" => "Shield",
            "code" => 12,
            "image" => "build-13.png",
            "description" => "The Defense Tower is a tall and imposing structure, equipped with advanced surveillance and defense technologies, protecting and ensuring the safety of people and the community. A testament to the coming together of science and engineering to protect and preserve life and liberty.",
            "effect" => "Enables the Commander guardian.With each level, increases the responsiveness to attacks against enemy invasions",
            "metalStart" => 100,
            "uraniumStart" => 50,
            "crystalStart" => 50,
            "metalLevel" => 1,
            "uraniumLevel" => 10,
            "crystalLevel" => 20,
            "coefficient" => 100
        ]);

        DB::table('builds')->insert([
            "name" => "Market",
            "code" => 13,
            "image" => "build-18.png",
            "description" => "Resource trades between players",
            "effect" => "With each level of expansion, the fee rate is reduced and the trading range is increased",
            "metalStart" => 100,
            "uraniumStart" => 50,
            "crystalStart" => 50,
            "metalLevel" => 1,
            "uraniumLevel" => 10,
            "crystalLevel" => 20,
            "coefficient" => 50
        ]);

        DB::table('builds')->insert([
            "name" => "Galactic Council",
            "code" => 14,
            "image" => "build-14.png",
            "description" => "Nestled in the heart of the universe, the 'Galactic Conclave' is a shining beacon of hope and collaboration. This magnificent structure celebrates the essence of the human and intergalactic spirit, serving as a sacred gathering point for all cosmic explorers in search of powerful alliances. With its futuristic and gleaming architecture, the 'Galactic Conclave' radiates vibrant and welcoming energy, attracting players from every corner of the space. As travelers converge upon this center of unity, diversity intertwines with harmony, forming indestructible bonds between civilizations. Within its hallowed halls, secrets are shared, strategies are devised, and a symphony of cooperation resonates with each step. It is here, in the heart of the 'Galactic Conclave,' that the destiny of the galaxy is shaped by joined hands, writing a tale of unity, trust, and cosmic prosperity.",
            "effect" => "Allow to join and create alliances with other players",
            "metalStart" => 100,
            "uraniumStart" => 50,
            "crystalStart" => 50,
            "metalLevel" => 1,
            "uraniumLevel" => 10,
            "crystalLevel" => 20,
            "coefficient" => 50
        ]);
    }
}
