<?php
declare(strict_types = 1);

namespace App\Commands;

use App\Database\Database;
use App\Posts\Post;
use App\Posts\PostsRepository;
use App\Users\User;
use App\Users\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand('db:init', description: 'Init database tables')]
final class InitDatabaseCommand extends Command
{
    private array $userIds = [];
    public function __construct(
        private readonly Database $db,
        private readonly UserRepository $users,
        private readonly PostsRepository $posts,
    )
    {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->checkTables()) {
            $output->writeln('Tables already exist');

            return self::SUCCESS;
        }

        $output->writeln('Creating tables');
        $result = $this->createTables();
        if (!$result) {
            $output->writeln('Failed');
            return self::FAILURE;
        }

        $output->writeln('Success!');
        return self::SUCCESS;
    }

    private function checkTables(): bool
    {
        $sql = <<<SQL
          SELECT COUNT(*)
          FROM information_schema.tables
          WHERE table_schema = 'public' 
            AND table_name IN ('users', 'posts')
        SQL;

        $tablesCount = (int)$this->db->fetchValue($sql);

        return $tablesCount === 2;
    }

    private function createTables(): bool
    {
        try {
            $this->db->begin();
            $sql = <<<SQL
              CREATE TABLE users (
                id SERIAL PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE
              )
            SQL;

            $this->db->query($sql);
            $sql = <<<SQL
              CREATE TABLE posts (
                id SERIAL PRIMARY KEY,
                user_id INT NOT NULL REFERENCES users(id),
                title VARCHAR(255) NOT NULL,
                body TEXT
              )
            SQL;

            $this->db->query($sql);

            if (!$this->checkTables()) {
                $this->db->rollback();

                return false;
            }

            $this->fillUsers();
            $this->fillPosts();
            $this->db->commit();
            return true;
        } catch(Throwable $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    private function randomName(): string
    {
        $names = [ 'Alice', 'Bob', 'Charlie', 'David', 'Eve', 'Frank', 'Grace', 'Henry', 'Ivy', 'Jack' ];

        return $names[ array_rand($names) ];
    }

    private function randomEmail(): string
    {
        $emailsFirstPart = [ 'e-mail', 'mail', 'email' ];
        $emailsSecondPart = [ 'example.com', 'example.org', 'example.net' ];

        return sprintf(
            '%s-%s@%s',
            $emailsFirstPart[ array_rand($emailsFirstPart) ],
            random_int(1, 100),
            $emailsSecondPart[ array_rand($emailsSecondPart) ]
        );
    }

    private function fillUsers(): void
    {
        $usersCount = 10;
        while($usersCount-- > 0) {
            $user = User::new($this->randomName(), $this->randomEmail());
            $user = $this->users->add($user);
            $this->userIds[] = $user->id;
        }
    }

    private function randomPostTitle(): string
    {
        $titles = [ 'Post title', 'Another post title', 'Yet another post title' ];
        return sprintf('%s - %s', $titles[ array_rand($titles) ], random_int(1, 100));
    }

    private function randomPostBody(): string
    {
        $stringSpace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $length = random_int(100, 1000);
        $repeatTimes = (int) ceil($length / strlen($stringSpace));
        $string = str_repeat($stringSpace, $repeatTimes);
        $shuffledString = str_shuffle($string);
        return substr($shuffledString, 0, $length);
    }

    private function fillPosts(): void
    {
        $postsCount = 10;

        while($postsCount-- > 0) {
            $userId = $this->userIds[ array_rand($this->userIds) ];
            $post = Post::new($userId, $this->randomPostTitle(), $this->randomPostBody());
            $this->posts->add($post);
        }
    }
}
