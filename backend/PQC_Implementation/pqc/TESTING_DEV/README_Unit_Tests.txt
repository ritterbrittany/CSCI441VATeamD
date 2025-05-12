README: Unit Tests for Threefish-1024

Purpose:
This test file (test_threefish_unit.py) provides unit tests for core components of the manually implemented Threefish-1024 post-quantum cryptographic (PQC) encryption algorithm.

These tests are designed to verify the correctness of:
 - Bitwise rotations
 - Mix and unmix logic
 - Key and tweak expansion
 - Single-block encryption and decryption

Requirements:
 - Python 3.8 or later
 - threefish_manual_TESTING.py must be accessible in the same directory or importable from a parent folder

Command to Run: 
python -m unittest test_threefish_unit.py

You will see readable output like:
Running Threefish-1024 unit tests...
Testing rotl64 (left rotation)...
Testing rotr64 (right rotation)...
etc...

What Each Test Does:
 - rotl64 / rotr64: Ensures 64-bit rotate-left/right behave correctly
 - mix/unmix: Confirms reversible mixing
 - expand_key: Ensures 17 key words + 3 tweak values
 - encrypt_block/decrypt_block: Round-trip test for correctness

All tests should pass with no errors if the cryptographic core is implemented correctly.
