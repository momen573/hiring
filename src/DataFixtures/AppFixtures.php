<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\Type;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Load data in sequence to ensure dependencies are available
        $this->loadUsers($manager);
        $this->loadTypes($manager);
        $this->loadCategories($manager);

        // Ensure data is flushed to database
        $manager->flush();

        // Now load posts, which rely on users, types, and categories
        $this->loadPosts($manager);

        $manager->flush();
    }

    private function loadUsers(ObjectManager $manager): void
    {
        foreach ($this->getUserData() as [$username, $password, $roles]) {
            $user = new User();
            $user->setUsername($username);
            $user->setPassword($this->passwordHasher->hashPassword($user, $password));
            $user->setRoles($roles);

            $manager->persist($user);
        }
    }

    private function getUserData(): array
    {
        return [
            ['admin', 'adminpass', [User::ROLE_ADMIN]],
            ['user1', 'user1pass', [User::ROLE_USER]],
            ['user2', 'user2pass', [User::ROLE_USER]],
        ];
    }

    private function loadTypes(ObjectManager $manager): void
    {
        foreach ($this->getTypeData() as [$typeName, $description]) {
            $type = new Type();
            $type->setName($typeName);
            $type->setDescription($description);

            $manager->persist($type);
        }
    }

    private function getTypeData(): array
    {
        return [
            ['News', 'Latest updates and news articles.'],
            ['Article', 'In-depth articles about various topics.'],
            ['Blog', 'Personal or professional blog posts.'],
            ['Tutorial', 'Step-by-step guides for learning.'],
            ['Review', 'Reviews on products or services.'],
        ];
    }

    private function loadCategories(ObjectManager $manager): void
    {
        foreach ($this->getCategoryData() as [$categoryName, $description]) {
            $category = new Category();
            $category->setName($categoryName);
            $category->setDescription($description);

            $manager->persist($category);
        }
    }

    private function getCategoryData(): array
    {
        return [
            ['Technology', 'Articles related to technology trends and innovations.'],
            ['Health', 'Health-related content and wellness tips.'],
            ['Science', 'Science news and discoveries.'],
            ['Lifestyle', 'Lifestyle articles, tips, and trends.'],
            ['Education', 'Educational resources and materials.'],
            ['Business', 'Business-related news and strategies.'],
        ];
    }

    private function loadPosts(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Retrieve all existing categories, types, and users
        $categories = $manager->getRepository(Category::class)->findAll();
        $types = $manager->getRepository(Type::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        // Ensure that categories, types, and users are not empty
        if (empty($categories) || empty($types) || empty($users)) {
            throw new \RuntimeException('Categories, Types, or Users data are missing.');
        }

        for ($i = 0; $i < 30; $i++) {
            $post = new Post();
            $post->setTitle($faker->sentence);
            $post->setContent($faker->paragraphs(3, true));

            // Use dateTimeBetween and convert it to DateTimeImmutable
            $post->setPublishedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')));

            // Assign random categories, types, and user
            $post->addCategory($faker->randomElement($categories));
            $post->addType($faker->randomElement($types));
            $post->setUser($faker->randomElement($users));

            $manager->persist($post);
        }
    }
}
