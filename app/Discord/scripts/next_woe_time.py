import discord
import os
import requests
from datetime import datetime, timedelta
from discord.ext import tasks
import sys
import json

token = sys.argv[1]
url = sys.argv[2]
woe_times_data_json = sys.argv[3]

# Convert JSON string to a Python dictionary
woe_times_data = json.loads(woe_times_data_json)

# Convert the received data into the correct format for further processing
woe_times_data = {
    castle: {
        'time': datetime.strptime(details['time'], '%H:%M') if isinstance(details['time'], str) else details['time'],
        'day': details.get('day', [None])[0]
    }
    for castle, details in woe_times_data.items()
    if details.get('time')
}

intents = discord.Intents.default()
intents.presences = True

client = discord.Client(intents=intents)
session = requests.Session()

@client.event
async def on_ready():
    print(f'Logged in as {client.user}')
    update_activity.start()

@tasks.loop(minutes=60)
async def update_activity():
    global woe_times_data  # Declare woe_times_data as a global variable

    if woe_times_data:
        current_time = datetime.now()

        # Create a new dictionary to store modified times
        modified_woe_times = {castle: {'time': time['time'] - timedelta(hours=1), 'day': time['day']} for castle, time in woe_times_data.items()}

        # Filter out entries where 'day' is None or not present using modified_woe_times
        valid_entries = [(details['time'], details['day']) for castle, details in modified_woe_times.items() if details['day'] is not None and details['time']]

        if valid_entries:
            # Find the next WoE time and day
            next_woe_time, next_woe_day = min(valid_entries)

            # Calculate the time difference in hours
            time_difference_hours = (next_woe_time - current_time).seconds // 3600

            # Format the time as a string
            time_str = current_time.strftime('%I:%M %p')

            # Display the time until the next WoE in hours along with the next WoE time and day
            next_woe_str = f'({next_woe_time.strftime("%H:%M %p")})'
            activity_text = f'in {time_difference_hours}hr. {next_woe_str}'

            activity = discord.Activity(name=activity_text, type=discord.ActivityType.watching)

            await client.change_presence(activity=activity)
        else:
            print("No valid WoE times data available.")
    else:
        print("No WoE times data available.")

try:
    client.run(token)
except discord.HTTPException as e:
    if e.status == 429:
        print("Too many requests")
    else:
        raise e
