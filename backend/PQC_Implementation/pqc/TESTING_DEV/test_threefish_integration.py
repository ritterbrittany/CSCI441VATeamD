# test_threefish_integration.py - Integration test for Threefish CLI

import subprocess
import os
import unittest

class TestThreefishIntegration(unittest.TestCase):

    def setUp(self):
        self.input_text = "This is a test message for integration."
        self.enc_file = "test_integration.enc"
        self.dec_file = "test_integration_dec.txt"
        self.key_file = "test_integration.key"

    def test_encrypt_decrypt_text_round_trip(self):
        # Encrypt
        subprocess.run([
            "python", "threefish_run_cli_EXTENDED.py", "encrypt",
            "--text", self.input_text,
            "--outfile", self.enc_file,
            "--keyfile", self.key_file
        ], check=True)

        # Decrypt
        subprocess.run([
            "python", "threefish_run_cli_EXTENDED.py", "decrypt",
            "--infile", self.enc_file,
            "--outfile", self.dec_file,
            "--keyfile", self.key_file
        ], check=True)

        # Verify
        with open(self.dec_file, 'rb') as f:
            output = f.read().rstrip(b'\x00').decode('utf-8')

        if output == self.input_text:
            print("\nSuccess! Decrypted file matches original plaintext!\n")
        else:
            print("\nFailure! Decrypted file does NOT match original plaintext!\n")
        self.assertEqual(output, self.input_text)

    def tearDown(self):
        for f in [self.enc_file, self.dec_file, self.key_file]:
            if os.path.exists(f):
                os.remove(f)

if __name__ == '__main__':
    unittest.main()