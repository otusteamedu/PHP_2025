<?php

declare(strict_types=1);

namespace Dinargab\Homework12\Render;

use Dinargab\Homework12\Model\Movie;

class MovieRenderer
{
    public static function render(Movie $movie): string
    {
        $output = "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; width: 100%;'>\n";
        $output .= "<tr><th colspan='2' style='background-color: #f2f2f2; text-align: center;'>" . htmlspecialchars($movie->getTitle()) . "</th></tr>\n";
        $output .= "<tr><td><strong>ID:</strong></td><td>" . ($movie->getId() ?? 'N/A') . "</td></tr>\n";
        $output .= "<tr><td><strong>Title:</strong></td><td>" . htmlspecialchars($movie->getTitle()) . "</td></tr>\n";
        $output .= "<tr><td><strong>Overview:</strong></td><td>" . htmlspecialchars($movie->getOverview()) . "</td></tr>\n";
        $output .= "<tr><td><strong>Release Date:</strong></td><td>" . $movie->getReleaseDate()->format('Y-m-d') . "</td></tr>\n";
        $output .= "<tr><td><strong>Duration:</strong></td><td>" . $movie->getDuration(true) . " (" . $movie->getDuration() . " minutes)</td></tr>\n";
        $output .= "<tr><td><strong>Rating:</strong></td><td>" . number_format($movie->getRating(), 1) . "/10.0</td></tr>\n";
        $output .= "</table>\n";

        return $output;
    }

    public static function renderList(array $movies): string
    {
        if (empty($movies)) {
            return "<p>No movies found.</p>\n";
        }

        $output = "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; width: 100%;'>\n";
        $output .= "<thead>\n";
        $output .= "<tr style='background-color: #f2f2f2;'>\n";
        $output .= "<th>ID</th>\n";
        $output .= "<th>Title</th>\n";
        $output .= "<th>Release Date</th>\n";
        $output .= "<th>Duration</th>\n";
        $output .= "<th>Rating</th>\n";
        $output .= "</tr>\n";
        $output .= "</thead>\n";
        $output .= "<tbody>\n";

        foreach ($movies as $movie) {
            if (!$movie instanceof Movie) {
                continue;
            }

            $output .= "<tr>\n";
            $output .= "<td>" . ($movie->getId() ?? 'N/A') . "</td>\n";
            $output .= "<td>" . htmlspecialchars($movie->getTitle()) . "</td>\n";
            $output .= "<td>" . $movie->getReleaseDate()->format('Y-m-d') . "</td>\n";
            $output .= "<td>" . $movie->getDuration(true) . " (" . $movie->getDuration() . " minutes)</td>\n";
            $output .= "<td>" . number_format($movie->getRating(), 1) . "/10</td>\n";
            $output .= "</tr>\n";
        }

        $output .= "</tbody>\n";
        $output .= "</table>\n";

        return $output;
    }
}
