import math
import re
import numpy as np
import sympy as sp

SUPPORTED_FUNCTIONS = {
    'sin': math.sin,
    'cos': math.cos,
    'tan': math.tan,
    'asin': math.asin,
    'acos': math.acos,
    'atan': math.atan,
    'sqrt': math.sqrt,
    'log': math.log10,
    'ln': math.log,
    'exp': math.exp,
    'factorial': math.factorial,
    'pi': math.pi,
    'e': math.e,
}


class CalculatorEngine:
    def __init__(self):
        self.history = []
        self.memory = 0.0

    def evaluate(self, expression: str) -> str:
        expression = expression.strip()
        if not expression:
            return ""

        if expression.startswith('bin(') or expression.startswith('oct(') or expression.startswith('hex('):
            return self._evaluate_base(expression)

        normalized = self._normalize_expression(expression)
        result = self._safe_eval(normalized)
        self._record_history(expression, result)
        return self._format_result(result)

    def _normalize_expression(self, expression: str) -> str:
        expr = expression.replace('×', '*').replace('÷', '/').replace('−', '-')
        expr = expr.replace('^', '**')
        expr = expr.replace('pi', 'pi')
        expr = re.sub(r'(\d+(?:\.\d+)?)!', r'factorial(\1)', expr)
        return expr

    def _safe_eval(self, expression: str):
        allowed = {
            **SUPPORTED_FUNCTIONS,
            'abs': abs,
            'round': round,
            'pow': pow,
            'sqrt': math.sqrt,
            'pi': math.pi,
            'e': math.e,
            'factorial': math.factorial,
        }
        allowed['np'] = np
        return eval(expression, {'__builtins__': None}, allowed)

    def _format_result(self, value) -> str:
        if isinstance(value, float) and value.is_integer():
            return str(int(value))
        return str(value)

    def _evaluate_base(self, expression: str) -> str:
        if expression.startswith('bin('):
            value = self._safe_eval(expression[4:-1])
            return bin(int(value))
        if expression.startswith('oct('):
            value = self._safe_eval(expression[4:-1])
            return oct(int(value))
        if expression.startswith('hex('):
            value = self._safe_eval(expression[4:-1])
            return hex(int(value))
        raise ValueError('Unsupported base conversion')

    def solve_equation(self, equation: str):
        x = sp.symbols('x')
        expr = sp.sympify(equation.replace('=', '-(') + ')')
        solutions = sp.solve(expr, x)
        return solutions

    def matrix_add(self, a, b):
        return np.add(a, b)

    def matrix_multiply(self, a, b):
        return np.matmul(a, b)

    def to_binary(self, value):
        return bin(int(value))

    def to_octal(self, value):
        return oct(int(value))

    def to_hex(self, value):
        return hex(int(value))

    def memory_store(self, value):
        self.memory = value

    def memory_recall(self):
        return self.memory

    def memory_clear(self):
        self.memory = 0.0

    def _record_history(self, expression, result):
        self.history.append((expression, self._format_result(result)))
        if len(self.history) > 50:
            self.history.pop(0)
