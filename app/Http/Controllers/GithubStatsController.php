<?php

namespace App\Http\Controllers;

use GrahamCampbell\GitHub\Facades\GitHub;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GithubStatsController extends Controller
{
    public function getTotalCommits(Request $request)
    {
        // Logic to fetch total commits from GitHub API
        $username = $request->route("username");
        $from = $request->input("from", "2019-01-01T00:00:00Z");
        $to = $request->input("to", now()->toIso8601String());

        $cacheKey = 'github_total_commits_' . $username . '_' . $from . '_' . $to;
        $total = Cache::remember($cacheKey, 60, function () use ($username, $from, $to) {
            $query = <<<'GRAPHQL'
            query($username: String!, $from: DateTime!, $to: DateTime!) {
                user(login: $username) {
                    contributionsCollection(from: $from, to: $to) {
                        totalCommitContributions
                        restrictedContributionsCount
                    }
                }
            }
        GRAPHQL;

            $variablesList = [];
            $start = \Carbon\Carbon::parse($from);
            $end = \Carbon\Carbon::parse($to);

            while ($start->lt($end)) {
                $yearStart = $start->copy();
                $yearEnd = $start->copy()->endOfYear();
                if ($yearEnd->gt($end)) {
                    $yearEnd = $end->copy();
                }
                $variablesList[] = [
                    'username' => $username,
                    'from' => $yearStart->toIso8601String(),
                    'to' => $yearEnd->toIso8601String(),
                ];

                $start = $yearEnd->addSecond();
            }

            $totalCommits = 0;
            foreach ($variablesList as $variables) {
                $result = GitHub::api('graphql')->execute($query, $variables);
                $data = $result['data']['user']['contributionsCollection'] ?? null;
                if ($data) {
                    $totalCommits += $data['totalCommitContributions'] ?? 0;
                }
            }

            return $totalCommits;
        });

        return response()->json([
            'total_commits' => $total,
            'username' => $username,
            'from' => $from,
            'to' => $to
        ]);
    }
}
