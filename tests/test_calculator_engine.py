import unittest

from calculator import CalculatorEngine


class CalculatorEngineTest(unittest.TestCase):
    def test_scientific_expression_support(self):
        engine = CalculatorEngine()
        self.assertEqual(engine.evaluate('2 + 3 * 4'), '14')
        self.assertEqual(engine.evaluate('sqrt(9)'), '3')
        self.assertEqual(engine.evaluate('2^3'), '8')
        self.assertEqual(engine.evaluate('sin(pi/2)'), '1')


if __name__ == '__main__':
    unittest.main()
