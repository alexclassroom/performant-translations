import { Scenario, type TestCase } from './types';

export const testCases: TestCase[] = [
	{ locale: 'en_US', scenario: Scenario.Default },
	{ locale: 'de_DE', scenario: Scenario.Default },
	{ locale: 'de_DE', scenario: Scenario.GingerMo },
	{ locale: 'de_DE', scenario: Scenario.GingerMoPhp },
];

type IterationCallback = () => Promise<Record<string, number>>;

export async function iterate(
	cb: IterationCallback,
	iterations = Number(process.env.TEST_RUNS)
) {
	const result: Record<string, number[]> = {};
	let i = iterations;

	while (i--) {
		const metrics = await cb();
		for (const [key, value] of Object.entries(metrics)) {
			result[key] ??= [];
			result[key].push(value);
		}
	}

	return Object.fromEntries(
		Object.entries(result).map(([key, value]) => [
			key,
			median(value as number[]),
		])
	);
}

/**
 * Computes the median number from an array numbers.
 *
 * @param array List of numbers.
 * @return Median.
 */
export function median(array: number[]) {
	const mid = Math.floor(array.length / 2);
	const numbers = [...array].sort((a, b) => a - b);
	const result =
		array.length % 2 !== 0
			? numbers[mid]
			: (numbers[mid - 1] + numbers[mid]) / 2;

	return Number(result.toFixed(2));
}
