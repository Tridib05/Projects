import importlib
import unittest


class UiImportTest(unittest.TestCase):
    def test_ui_imports_without_crashing(self):
        module = importlib.import_module('ui')
        self.assertTrue(hasattr(module, 'CalculatorApp'))


if __name__ == '__main__':
    unittest.main()
