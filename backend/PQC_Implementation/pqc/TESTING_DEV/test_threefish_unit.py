# test_threefish_unit.py - Unit tests for Threefish-1024 core functions

import unittest
from threefish_manual_TESTING import rotl64, rotr64, mix, unmix, expand_key, encrypt_block, decrypt_block

class TestThreefishCore(unittest.TestCase):

    def test_rotl64(self):
        print("Testing rotl64 (left rotation)...")
        self.assertEqual(rotl64(0x01, 1), 0x02)
        self.assertEqual(rotl64(0x8000000000000000, 1), 0x0000000000000001)

    def test_rotr64(self):
        print("Testing rotr64 (right rotation)...")
        self.assertEqual(rotr64(0x02, 1), 0x01)
        self.assertEqual(rotr64(0x0000000000000001, 1), 0x8000000000000000)

    def test_mix_unmix_inverse(self):
        print("Testing mix and unmix functions (reversibility)...")
        x0, x1 = 0x0123456789ABCDEF, 0xFEDCBA9876543210
        y0, y1 = mix(x0, x1, 24)
        z0, z1 = unmix(y0, y1, 24)
        self.assertEqual((z0, z1), (x0, x1))

    def test_expand_key_length(self):
        print("Testing expand_key output lengths...")
        key = list(range(16))
        tweak = [1, 2]
        k_sched, t_sched = expand_key(key, tweak)
        self.assertEqual(len(k_sched), 17)
        self.assertEqual(len(t_sched), 3)

    def test_encrypt_decrypt_round_trip(self):
        print("Testing encrypt_block and decrypt_block round-trip...")
        key = list(range(16))
        tweak = [123456789, 987654321]
        block = list(range(16))
        encrypted = encrypt_block(block, key, tweak)
        decrypted = decrypt_block(encrypted, key, tweak)
        self.assertEqual(decrypted, block)

if __name__ == '__main__':
    print("Running Threefish-1024 unit tests...")
    unittest.main(verbosity=2)
