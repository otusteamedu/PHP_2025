CREATE OR REPLACE FUNCTION get_random_datetime(seed int,
        start_ts timestamp DEFAULT '2020-01-01 00:00:00',
        end_ts   timestamp DEFAULT '2025-12-31 00:00:00')
RETURNS timestamp(0)
LANGUAGE sql VOLATILE
AS $$
    SELECT (start_ts
         + random()
         * (end_ts - start_ts))::timestamp(0);
$$;


CREATE OR REPLACE FUNCTION get_random_film()
RETURNS int
LANGUAGE sql
AS $$
    SELECT (random() * 100)::int;
$$;


CREATE OR REPLACE FUNCTION get_random_price()
RETURNS int
LANGUAGE sql
AS $$
    SELECT floor(random() * 100)*10+100::int;
$$;




CREATE OR REPLACE FUNCTION get_random_extra_price()
RETURNS float
LANGUAGE plpgsql
AS $$
DECLARE
    words float[] := ARRAY[1.0,1.5,2.0,2.5];
BEGIN
    RETURN words[1 + floor(random() * array_length(words,1))];
END;
$$;


CREATE OR REPLACE FUNCTION get_film(id int)
RETURNS text
LANGUAGE plpgsql
AS $$
DECLARE
    words text[] := ARRAY[
                            'Avatar',
                            'Avengers: Endgame',
                            'Avatar: The Way of Water',
                            'Titanic',
                            'Ne Zha 2',
                            'Star Wars: Episode VII - The Force Awakens',
                            'Avengers: Infinity War',
                            'Spider-Man: No Way Home',
                            'Inside Out 2',
                            'Jurassic World',
                            'The Lion King',
                            'The Avengers',
                            'Furious 7',
                            'Top Gun: Maverick',
                            'Frozen II',
                            'Barbie',
                            'Avengers: Age of Ultron',
                            'The Super Mario Bros. Movie',
                            'Black Panther',
                            'Harry Potter and the Deathly Hallows: Part 2',
                            'Deadpool and Wolverine',
                            'Star Wars: Episode VIII - The Last Jedi',
                            'Jurassic World: Fallen Kingdom',
                            'Frozen',
                            'Beauty and the Beast',
                            'Incredibles 2',
                            'The Fate of the Furious',
                            'Iron Man 3',
                            'Minions',
                            'Captain America: Civil War',
                            'Aquaman',
                            'The Lord of the Rings: The Return of the King',
                            'Spider-Man: Far from Home',
                            'Captain Marvel',
                            'Transformers: Dark of the Moon',
                            'The Dark Knight Rises',
                            'Skyfall',
                            'Transformers: Age of Extinction',
                            'Jurassic Park',
                            'Joker',
                            'Star Wars: Episode IX - The Rise of Skywalker',
                            'Toy Story 4',
                            'Toy Story 3',
                            'Pirates of the Caribbean: Dead Mans Chest',
                            'Moana 2',
                            'Rogue One: A Star Wars Story',
                            'Aladdin',
                            'Pirates of the Caribbean: On Stranger Tides',
                            'Star Wars: Episode I - The Phantom Menace',
                            'Lilo and Stitch',
                            'Despicable Me 3',
                            'Finding Dory',
                            'Harry Potter and the Sorcerers Stone',
                            'Zootopia',
                            'Alice in Wonderland',
                            'The Hobbit: An Unexpected Journey',
                            'The Hobbit: An Unexpected Journey',
                            'The Dark Knight',
                            'Jurassic World: Dominion',
                            'The Lion King',
                            'Oppenheimer',
                            'Despicable Me 4',
                            'Despicable Me 2',
                            'The Jungle Book',
                            'Jumanji: Welcome to the Jungle',
                            'The Hobbit: The Battle of the Five Armies',
                            'Pirates of the Caribbean: At Worlds End',
                            'Harry Potter and the Deathly Hallows: Part 1',
                            'The Hobbit: The Desolation of Smaug',
                            'A Minecraft Movie',
                            'Doctor Strange in the Multiverse of Madness',
                            'Harry Potter and the Order of the Phoenix',
                            'Finding Nemo',
                            'Harry Potter and the Half-Blood Prince',
                            'Minions: The Rise of Gru',
                            'The Lord of the Rings: The Two Towers',
                            'Shrek 2',
                            'Bohemian Rhapsody',
                            'Star Wars: Episode III - Revenge of the Sith',
                            'The Battle at Lake Changjin',
                            'Harry Potter and the Goblet of Fire',
                            'Spider-Man 3',
                            'The Lord of the Rings: The Fellowship of the Ring',
                            'Ice Age: Dawn of the Dinosaurs',
                            'Harry Potter and the Chamber of Secrets',
                            'Spider-Man: Homecoming',
                            'Spectre',
                            'Ice Age: Continental Drift',
                            'The Secret Life of Pets',
                            'Batman v Superman: Dawn of Justice',
                            'Wolf Warrior 2',
                            'Jurassic World: Rebirth',
                            'The Hunger Games: Catching Fire',
                            'Guardians of the Galaxy Vol. 2',
                            'Black Panther: Wakanda Forever',
                            'Inside Out',
                            'Venom',
                            'Thor: Ragnarok',
                            'The Twilight Saga: Breaking Dawn - Part 2',
                            'Guardians of the Galaxy Vol. 3',
                            'Inception',
                            'Transformers: Revenge of the Fallen',
                            'Spider-Man',
                            'Mission: Impossible - Fallout',
                            'Wonder Woman',
                            'Hi, Mom',
                            'Independence Day',
                            'Fantastic Beasts and Where to Find Them',
                            'Coco',
                            'Harry Potter and the Prisoner of Azkaban',
                            'Shrek the Third',
                            'Jumanji: The Next Level',
                            'The Da Vinci Code',
                            'E.T. the Extra-Terrestrial',
                            'Pirates of the Caribbean: Dead Men Tell No Tales',
                            '2012',
                            'Fast and Furious 6',
                            'Indiana Jones and the Kingdom of the Crystal Skull',
                            'Deadpool 2',
                            'Spider-Man 2',
                            'Deadpool',
                            'Star Wars: Episode IV - A New Hope',
                            'No Time to Die',
                            'Guardians of the Galaxy',
                            'The Batman',
                            'Thor: Love and Thunder',
                            'Fast and Furious Presents: Hobbs and Shaw',
                            'Maleficent',
                            'Interstellar',
                            'The Hunger Games: Mockingjay - Part 1',
                            'The Amazing Spider-Man',
                            'Wicked',
                            'Shrek Forever After',
                            'Suicide Squad',
                            'Madagascar 3: Europes Most Wanted',
                            'X-Men: Days of Future Past',
                            'The Chronicles of Narnia: The Lion, the Witch and the Wardrobe',
                            'Monsters University',
                            'The Matrix Reloaded',
                            'Up',
                            'F9: The Fast Saga',
                            'Ne Zha',
                            'Gravity',
                            'Mufasa: The Lion King',
                            'The Amazing Spider-Man 2',
                            'Dune: Part Two',
                            'Captain America: The Winter Soldier',
                            'The Twilight Saga: Breaking Dawn - Part 1',
                            'The Twilight Saga: New Moon',
                            'Mission: Impossible - Rogue Nation',
                            'Dawn of the Planet of the Apes',
                            'Transformers',
                            'Mamma Mia!',
                            'Fast X',
                            'It',
                            'The Wandering Earth',
                            'The Twilight Saga: Eclipse',
                            'The Hunger Games',
                            'Mission: Impossible - Ghost Protocol',
                            'Spider-Man: Across the Spider-Verse',
                            'Detective Chinatown 3',
                            'Forrest Gump',
                            'Doctor Strange',
                            'The Sixth Sense',
                            'Full River Red',
                            'Man of Steel',
                            'Kung Fu Panda 2',
                            'The Hunger Games: Mockingjay - Part 2',
                            'Justice League',
                            'Big Hero 6',
                            'Fantastic Beasts: The Crimes of Grindelwald',
                            'Pirates of the Caribbean: The Curse of the Black Pearl',
                            'Men in Black³',
                            'Star Wars: Episode II - Attack of the Clones',
                            'Thor: The Dark World',
                            'Moana',
                            'How to Train Your Dragon',
                            'Wonka',
                            'Sing',
                            'Kung Fu Panda',
                            'The Incredibles',
                            'The Martian',
                            'Hancock',
                            'Water Gate Bridge',
                            'Fast Five',
                            'Iron Man 2',
                            'Ratatouille',
                            'F1: The Movie',
                            'Ant-Man and the Wasp',
                            'How to Train Your Dragon 2',
                            'Logan',
                            'The Lost World: Jurassic Park',
                            'Casino Royale',
                            'Superman',
                            'The Wandering Earth II',
                            'The Passion of the Christ',
                            'Life of Pi',
                            'Ready Player One',
                            'Transformers: The Last Knight',
                            'Madagascar: Escape 2 Africa'
    ];
BEGIN
    RETURN words[id];
END;
$$;
