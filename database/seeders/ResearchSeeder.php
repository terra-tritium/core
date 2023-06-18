<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResearchSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        DB::table('researchs')->insert([
            'code' => 100,
            'title' => 'Energy Saving',
            'description' => 'Renewable energy is a marvel of human ingenuity, capable of harnessing the vast sources of energy that nature offers us in a clean and sustainable way. It is a promise of a greener and more resilient future, where we can meet our energy needs without compromising the planet we live on.',
            'cost' => 100,
            'dependence' => 0,
            'category' => 2,
            'effectDescription' => 'Increases energy efficiency by 5%',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 200,
            'title' => 'Extraction of Noble Ores',
            'description' => "The extraction of noble ore is the quest for hidden treasures buried deep within planets, where precious materials that adorn our universe are found, providing us with the raw materials for today's and tomorrow's advanced technologies.",
            'cost' => 100,
            'dependence' => 100,
            'category' => 2,
            'effectDescription' => 'Enables Uranium and Crystal mining.',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 300,
            'title' => 'Energy Force Field',
            'description' => 'A force field is like a protective cloak, a web of luminous lines that wrap around an object, creating an energy shield that envelops and safeguards. It is a spectacular phenomenon of physics, a spectacle of light and color, maintaining balance and stability in the universe.',
            'cost' => 100,
            'dependence' => 0,
            'category' => 1,
            'effectDescription' => 'Enables the construction of a Force Shield and the Defense Tower to protect the planet against external attacks',
            'effects' => json_encode([
                'enableForceShield' => true,
                'enableDefenseTower' => true
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 400,
            'title' => 'Space Mechanics',
            'description' => 'Space Mechanics is a symphony of technology and design, where science and imagination come together to create astonishing machines that travel and explore the cosmos. It is the realization of human ambition to reach the limits of the universe and discover new forms of life and culture. With its futuristic vision and technological elegance, space mechanics resonates like flying operas, taking humanity to unimaginable places.',
            'cost' => 100,
            'dependence' => 300,
            'category' => 1,
            'effectDescription' => 'Enables the construction of the Shipyard',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 500,
            'title' => 'Colonization',
            'description' => 'Colonization, a daring journey in search of new horizons, challenges the limits of human exploration and takes us to forge a future beyond the stars. In the quest for new homes, colonization transcends borders and invites us to leave our mark in the vast cosmos, building civilizations where the unknown once reigned.',
            'cost' => 100,
            'dependence' => 0,
            'category' => 3,
            'effectDescription' => 'Enables the colonization of new planets',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 600,
            'title' => 'Advanced Robotics',
            'description' => "Advanced robotics is a symphony of metal and intelligence, where technology and science come together to create mechanical beings with astonishing abilities. It's like watching an orchestra of machines, performing precise movements and working in perfect synchrony to achieve goals that would be impossible for humanity alone. It's the realization of human imagination becoming reality.",
            'cost' => 100,
            'dependence' => 400,
            'category' => 1,
            'effectDescription' => 'Enables the construction of the Military Training Camp',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 700,
            'title' => 'Trade',
            'description' => 'Trade technology is a powerful catalyst for human progress. By connecting people and cultures, facilitating exchanges, and stimulating economic growth, it plays a vital role in building a more interconnected and prosperous future for all.',
            'cost' => 100,
            'dependence' => 500,
            'category' => 3,
            'effectDescription' => 'Enables the construction of the Market',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 800,
            'title' => 'Nanotechnology',
            'description' => 'Nanotechnology, a miniature universe, unveils a fascinating panorama where tiny particles come to life to revolutionize the world we know. An invisible ocean of possibilities, where atoms and molecules dance in harmony, bringing extraordinary advancements to humanity.',
            'cost' => 100,
            'dependence' => 600,
            'category' => 1,
            'effectDescription' => 'Enables the development of next generation humanoid robots.',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 900,
            'title' => 'Expansion',
            'description' => 'Expansion, an unstoppable call beyond familiar frontiers, awakens the audacity of explorers and visionaries, unveiling new horizons and challenging the limits of human imagination. Like an unrelenting flame, expansion ignites the soul of humanity, propelling us to seek new territories, resources, and possibilities, venturing into the unknown and writing bold chapters in the history of evolution.',
            'cost' => 100,
            'dependence' => 300,
            'category' => 2,
            'effectDescription' => 'Enables the utilization of the Builder Game Mode',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 1000,
            'title' => 'Tritium',
            'description' => 'The mineral Tritium is a precious gem found only in a few galaxies, with extraordinary properties that challenge our understanding of science. It is a gift from the heavens, shining with a mysterious light and granting powers never before imagined, hiding incredible secrets and unexplored possibilities. It is like holding a magical key to the mysteries of the universe, waiting to be unveiled and used to change the course of history.',
            'cost' => 100,
            'dependence' => 500,
            'category' => 3,
            'effectDescription' => 'Enables Tritium mining.',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 1100,
            'title' => 'Battery',
            'description' => 'The battery, a compact source of energy, is the spark that fuels the technological revolution, empowering our devices to explore the world with freedom and efficiency. With its hidden power, the battery becomes the pulsating heart of modern advancements, freeing us from the shackles of wires and enabling our devices to come alive, ready to challenge the limits of innovation.',
            'cost' => 100,
            'dependence' => 800,
            'category' => 1,
            'effectDescription' => 'Enables the construction of the Battery House',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 1200,
            'title' => 'Diplomacy',
            'description' => 'Diplomacy, a subtle and powerful art, transcends borders and builds bridges between nations, turning differences into opportunities for mutual understanding and lasting peace. Through diplomacy, words become instruments of harmony, and dialogue emerges as the key to resolving conflicts, forging strong relationships based on respect, cooperation, and shared prosperity.',
            'cost' => 100,
            'dependence' => 900,
            'category' => 2,
            'effectDescription' => 'Enables the construction of the Galactic Council, allowing participation and the creation of alliances',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        // DB::table('researchs')->insert([
        //     'code' => 1250,
        //     'title' => 'Laser',
        //     'description' => 'The laser, a remarkable scientific achievement, displays a concentrated and intense light, exploring new technological horizons with precision and beauty. Its versatility revolutionizes medicine, industry, and communication, illuminating the path to a future of endless possibilities.',
        //     'cost' => 100,
        //     'dependence' => 1100,
        //     'category' => 1,
        //     'effectDescription' => 'Enables the development of humanoid robots with laser ability.',
        //     'effects' => json_encode([
        //         'speedTravel' => 1,
        //     ]),
        // ]);

        DB::table('researchs')->insert([
            'code' => 1300,
            'title' => 'Plasma',
            'description' => 'Plasma, an exotic state of matter, unleashes a frenetic dance of electrifying particles, releasing an incandescent energy that illuminates the frontier between science and fiction. Like celestial fire, plasma, with its energetic and unpredictable nature, offers endless possibilities, whether in nuclear fusion, the creation of futuristic displays, or the exploration of the universe beyond the bounds of gravity.',
            'cost' => 100,
            'dependence' => 1200,
            'category' => 1,
            'effectDescription' => 'Enables the development of state-of-the-art Vehicles.',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 1400,
            'title' => 'Storage Center',
            'description' => 'The Storage Center, an imposing and secure fortress, guards the precious secrets and vital resources of humanity, protecting the legacy of the past and the aspirations of the future. Like a pulsating heart of operation, the Storage Center is the epicenter where technology and security converge, housing vast quantities of strategic supplies to face the challenges that await beyond its doors.',
            'cost' => 100,
            'dependence' => 1000,
            'category' => 3,
            'effectDescription' => 'Enables the construction of the Warehouse',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 1500,
            'title' => 'Gravity',
            'description' => 'Gravity, an invisible force that shapes the fabric of the universe, binds celestial bodies and gives form to cosmic movements, keeping us anchored to a reality that challenges us to soar beyond our limitations. Like an invisible embrace of nature, gravity exerts its influence on everything around us, from the motions of planets to our very own footsteps, constantly reminding us of the force that keeps us connected to the vastness of the cosmos.',
            'cost' => 100,
            'dependence' => 1300,
            'category' => 2,
            'effectDescription' => 'Enables the utilization of the Stellar Navigator Game Mode',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 1600,
            'title' => 'Light Reflection',
            'description' => 'The reflection of light, a radiant and ephemeral spectacle, transforms the world around us into a kaleidoscope of colors, revealing the magic contained within the graceful and enchanting dance of luminous rays. Like a mirror of the universe, the reflection of light captivates us and envelops us in its brilliant symphony, reflecting the hidden beauty of objects and evoking a sense of wonder in the magical interaction between light and surfaces.',
            'cost' => 100,
            'dependence' => 1400,
            'category' => 3,
            'effectDescription' => 'Increases energy efficiency by 5%',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 1700,
            'title' => 'Wisdom',
            'description' => "Wisdom, a radiant light in the darkness of ignorance, is a treasure accumulated through experiences, knowledge, and profound understanding, illuminating paths and guiding thirsty minds in search of answers. Like an eternal flame, wisdom transcends time, enriching souls eager for learning and offering wise guidance in the face of life's challenges, allowing hearts and minds to flourish with discernment and clarity.",
            'cost' => 100,
            'dependence' => 1500,
            'category' => 2,
            'effectDescription' => 'Allows the use of Researcher gameplay mode',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 1800,
            'title' => 'Hyperspeed',
            'description' => 'Hyperspeed, a frontier shattered by human ingenuity, takes us beyond conventional limits, opening portals to a universe of transcendent speed where time and space merge in a challenging race. Like a cosmic burst, hyperspeed breaks the shackles of slowness, allowing vessels to advance in a dizzying dance of speed and dexterity, defying distances and shortening time in an unwavering quest for new horizons.',
            'cost' => 100,
            'dependence' => 1300,
            'category' => 1,
            'effectDescription' => 'Increases spatial displacement efficiency by 5%.',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 1900,
            'title' => 'Factory',
            'description' => 'The factory, a constantly active production center, is the pulsating heart of modern manufacturing, where ingenuity and precision come together to bring products to life that drive our society. Like a hub of creation, the factory is the stage where ideas materialize into reality, where skilled machines weave the tapestry of production, propelling innovation and fueling progress.',
            'cost' => 100,
            'dependence' => 1700,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 2000,
            'title' => 'Govern',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 1900,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 2100,
            'title' => 'Locator',
            'description' => 'The locator, a precision tool in a world of possibilities, guides us through unknown landscapes, revealing hidden secrets and pointing the way to desired destinations. Like a reliable beacon, the locator illuminates the unknown, unveiling the invisible map that allows us to navigate uncharted territories, finding hidden treasures and unraveling the mysteries that lie beyond sight.',
            'cost' => 100,
            'dependence' => 1600,
            'category' => 3,
            'effectDescription' => 'Increases space travel efficiency by 5%.',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 2200,
            'title' => 'Power Supply',
            'description' => 'The energy source, a pulsating core of inexhaustible power, fuels the world around us, driving life and imbuing every being and machine with the vitality needed to thrive. Like an infinite wellspring, the energy source gushes with the promise of unlimited potential, powering technology and civilization, enabling us to move forward into the future with renewed energy and endless possibilities.',
            'cost' => 100,
            'dependence' => 2000,
            'category' => 2,
            'effectDescription' => 'Increases energy efficiency by 5%',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 2300,
            'title' => 'Destroyer',
            'description' => 'Destroyer is an imposing stellar war technology, a colossal metal behemoth designed to sow chaos and destruction in the depths of space. With its imposing structure and sharp lines, it inspires fear and respect from its possessor. In a universe in conflict, its presence on the battlefield signals the inevitability of ruin for those who dare to challenge it.',
            'cost' => 100,
            'dependence' => 1800,
            'category' => 1,
            'effectDescription' => 'Enables the development of next generation warships',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 2400,
            'title' => 'Black Trade',
            'description' => '"Black Trade" is an underground realm of shady dealings, where secrets, illicit goods, and illegal services are exchanged in the shadows. Operating beyond the boundaries of the law, this market is a place of dangerous and risky transactions, where the darkest desires find their price and discretion is the most valuable currency.',
            'cost' => 100,
            'dependence' => 2200,
            'category' => 2,
            'effectDescription' => 'Enables the trading of spacecraft.',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 2500,
            'title' => 'Alien Technology',
            'description' => 'Alien Technology" is an enigma shrouded in mystery, an advanced manifestation of extraterrestrial intelligence that challenges our understanding. Its complex designs and extraordinary functionalities reveal knowledge beyond the limits of our own civilization, opening doors to new cosmic discoveries and possibilities.',
            'cost' => 100,
            'dependence' => 2300,
            'category' => 1,
            'effectDescription' => 'Enables advancements in futuristic research.',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 2600,
            'title' => 'Tritium Filter',
            'description' => 'The "Tritium Filter" is a revolutionary purification device designed to remove impurities and radioactive contaminants. With its advanced technology, this filter is capable of capturing and neutralizing tritium particles, ensuring nearly complete utilization of its energy potential and fantastic capabilities.',
            'cost' => 100,
            'dependence' => 2100,
            'category' => 3,
            'effectDescription' => 'Enables the use of Tritium Force.',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 2700,
            'title' => 'Avanço tecnologica 1',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 2400,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 2800,
            'title' => 'Avanço militar 1',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 2500,
            'category' => 1,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 2900,  
            'title' => 'Avanço espacial 1',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 2600,
            'category' => 3,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 3000,
            'title' => 'Space Mining 1',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 2700,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 3100,
            'title' => 'Avanço tecnologica 2',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 3000,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 3200,
            'title' => 'Avanço militar 2',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 2800,
            'category' => 1,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 3300,
            'title' => 'Avanço espacial 2',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 2900,
            'category' => 3,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 3400,
            'title' => 'Space Mining 2',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 3100,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 3500,
            'title' => 'Avanço tecnologica 3',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 3400,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 3600,
            'title' => 'Avanço militar 3',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 3200,
            'category' => 1,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);
        
        DB::table('researchs')->insert([
            'code' => 3700,
            'title' => 'Avanço espacial 3',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 3300,
            'category' => 3,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);

        DB::table('researchs')->insert([
            'code' => 3800,
            'title' => 'Space Mining 3',
            'description' => 'Descrição',
            'cost' => 100,
            'dependence' => 3500,
            'category' => 2,
            'effectDescription' => 'Aumenta a velocidade de viagem em 1',
            'effects' => json_encode([
                'speedTravel' => 1,
            ]),
        ]);
    }
}
