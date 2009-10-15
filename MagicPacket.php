<?php
	//	MagicPacket.php
	//	A PHP class for wake-on-LAN operations.

class MagicPacket {
	//	Class-scope variables
	var $destination_ip;
	var $destination_mac;
	var $magic_packet;
	
	function __construct($ip, $mac, $port = 7) {
		//	Assign class variables that were passed when the class was created
		$this->destination_ip = $ip;
		$this->destination_mac = $mac;
		$this->destination_port = $port;
	}
	
	function create_magic_packet() {
		/*	Magic Packet spec:
			Six bytes of FF + 16x target MAC
		*/
		
		for($x = 0; $x < 6; $x++) {
			$this->magic_packet .= chr(0xff);
		}
		
		//	Encode the MAC address
		$split_mac = explode(':', $this->destination_mac);
		
		foreach($split_mac as $mac_part) {
			$encoded_mac .= chr(hexdec($mac_part));
		}

		//	Add it to the magic packet
		for($x = 0; $x < 16; $x++) {
			$this->magic_packet .= $encoded_mac;
		}
		
	}
	
	function get_magic_packet() {
		if(strlen($this->magic_packet) > 0) {
			return $this->magic_packet;
		} else {
			return "Not yet generated";
		}		
	}
	
	function send_magic_packet() {
		//	Open a UDP socket
		$udp_handle = fsockopen("udp://" . $this->destination_ip, $this->destination_port, $error_number, $error_string);
		
		if(!($udp_handle)) {
			echo "Error opening socket: " . $error_string . "\n";
			return false;
		} else {
			fwrite($udp_handle, $this->create_magic_packet());
			fclose($udp_handle);
			return true;
		}
	}
	
	function print_vars() {
		//	Dump out the contents of the class
		echo "Destination IP: $this->destination_ip\n";
		echo "Destination MAC: $this->destination_mac\n";
		echo "Destination port: $this->destination_port\n";
		echo "Magic Packet: " . $this->get_magic_packet();
	}
}
	
?>