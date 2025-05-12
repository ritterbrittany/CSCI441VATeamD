README: Integration Tests for Threefish-1024 CLI

Purpose:
This integration test (test_threefish_integration.py) validates the full system behavior of the Threefish CLI by encrypting and decrypting a sample text string.

It simulates a real-world usage flow:
 - Invokes the CLI with a text string for encryption
 - Saves the encrypted file and key file
 - Uses the CLI to decrypt the result
 - Compares the output to the original plaintext

Requirements:
 - Python 3.8 or later
 - threefish_run_cli_EXTENDED.py must be in the same directory
 - threefish_manual_TESTING.py must be accessible or importable

Command to Run:
python -m unittest test_threefish_integration.py

Output Example:
Encryption complete. Key saved.
Decryption complete.

Success! Decrypted file matches original plaintext!

.
----------------------------------------------------------------------
Ran 1 test in 0.6s

OK

Files Used During Test:
 - test_integration.enc: Encrypted output
 - test_integration.key: Key and tweak used
 - test_integration_dec.txt: Final decrypted output

These files are auto-cleaned after the test, please modify the unittest package used to modify this accordingly for different custom results as desired.

The test will fail if any CLI component malfunctions or the encryption/decryption round-trip fails.